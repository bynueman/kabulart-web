<?php

namespace App\Http\Controllers;

use App\Models\Postinformasi;
use App\Services\MediaPipeline;
use App\Services\TranslationService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PostinformasiyController extends Controller
{
    protected TranslationService $translator;

    public function __construct(TranslationService $translator)
    {
        $this->translator = $translator;
    }

    public function index(): View
    {
        $posts = Postinformasi::latest()->get();

        return view('postinformasi', compact('posts'));
    }

    public function create(): View
    {
        return view('createinformasi');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image'     => 'required|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
            'deskripsi' => 'required|string',
        ]);

        try {
            $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');

            $sourceText = trim($request->deskripsi);
            $detectedSource = $this->translator->detectLanguage($sourceText);
            $translationFailed = false;

            if ($detectedSource === 'id') {
                $deskripsiId = $sourceText;
                $deskripsiEn = $this->translator->translate($sourceText, 'en', 'id');
                if (empty($deskripsiEn)) {
                    $translationFailed = true;
                    $deskripsiEn = null;
                }
            } else {
                $deskripsiEn = $sourceText;
                $deskripsiId = $this->translator->translate($sourceText, 'id', 'en');
                if (empty($deskripsiId)) {
                    $translationFailed = true;
                    $deskripsiId = null;
                }
            }

            Postinformasi::create([
                'image'              => $optimized['filename'],
                'deskripsi'          => $sourceText,
                'deskripsi_id'       => $deskripsiId,
                'deskripsi_en'       => $deskripsiEn,
                'translation_source' => $detectedSource,
                'translation_manual' => false,
            ]);

            if ($translationFailed) {
                return redirect()->route('postsinformasi.index')->with([
                    'success' => __('flash.saved_success'),
                    'warning' => __('flash.translation_warning'),
                ]);
            }

            return redirect()->route('postsinformasi.index')->with([
                'success' => __('flash.saved_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Informasi image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function edit($id): View
    {
        $post = Postinformasi::findOrFail($id);
        return view('editinformasi', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image'        => 'nullable|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
            'deskripsi'    => 'nullable|string',
            'deskripsi_id' => 'required_without:deskripsi|nullable|string',
            'deskripsi_en' => 'required_without:deskripsi|nullable|string',
        ]);

        $post = Postinformasi::findOrFail($id);

        try {
            $oldImage = $post->image;
            $newImageUploaded = false;

            if ($request->hasFile('image')) {
                $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');
                $post->image = $optimized['filename'];
                $newImageUploaded = true;
            }

            // Handle dual bilingual input from edit form
            if ($request->filled('deskripsi_id') || $request->filled('deskripsi_en')) {
                $newId = trim($request->input('deskripsi_id', ''));
                $newEn = trim($request->input('deskripsi_en', ''));

                $idChanged = ($newId !== (string)$post->deskripsi_id);
                $enChanged = ($newEn !== (string)$post->deskripsi_en);

                $post->deskripsi_id = $newId ?: null;
                $post->deskripsi_en = $newEn ?: null;
                $post->deskripsi    = $newId ?: ($newEn ?: $post->deskripsi);

                if ($idChanged || $enChanged) {
                    $post->translation_manual = true;
                }
            } elseif ($request->filled('deskripsi')) {
                // Single input fallback
                $sourceText = trim($request->deskripsi);
                $detectedSource = $this->translator->detectLanguage($sourceText);
                $post->deskripsi = $sourceText;
                $post->translation_source = $detectedSource;

                if ($detectedSource === 'id') {
                    $post->deskripsi_id = $sourceText;
                    $trans = $this->translator->translate($sourceText, 'en', 'id');
                    if (!empty($trans)) {
                        $post->deskripsi_en = $trans;
                    }
                } else {
                    $post->deskripsi_en = $sourceText;
                    $trans = $this->translator->translate($sourceText, 'id', 'en');
                    if (!empty($trans)) {
                        $post->deskripsi_id = $trans;
                    }
                }
            }

            $post->save();

            // Only delete old variants after new image and model have been successfully saved
            if ($newImageUploaded && !empty($oldImage) && $oldImage !== $post->image) {
                MediaPipeline::deleteVariants($oldImage, 'postsimg');
            }

            return redirect()->route('postsinformasi.index')->with([
                'success' => __('flash.updated_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Informasi image update optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Postinformasi::findOrFail($id);
        $imageToDelete = $post->image;

        $post->delete();

        if (!empty($imageToDelete)) {
            try {
                MediaPipeline::deleteVariants($imageToDelete, 'postsimg');
            } catch (\Throwable $e) {
                Log::warning('Error deleting informasi image variants: ' . $e->getMessage());
            }
        }

        return redirect()->route('postsinformasi.index')->with([
            'success' => __('flash.deleted_success'),
        ]);
    }
}

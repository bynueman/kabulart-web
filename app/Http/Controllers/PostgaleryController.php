<?php

namespace App\Http\Controllers;

use App\Models\Postgalery;
use App\Services\MediaPipeline;
use App\Services\TranslationService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PostgaleryController extends Controller
{
    protected TranslationService $translator;

    public function __construct(TranslationService $translator)
    {
        $this->translator = $translator;
    }

    public function index(): View
    {
        $posts = Postgalery::latest()->get();

        return view('postgallery', compact('posts'));
    }

    public function create(): View
    {
        return view('creategalery');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image'   => 'required|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
            'nama'    => 'required|string|max:255',
            'dimensi' => 'required|string|max:255',
            'link'    => 'required|string',
        ]);

        try {
            $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');

            $sourceName = trim($request->nama);
            $detectedSource = $this->translator->detectLanguage($sourceName);
            $translationFailed = false;

            if ($detectedSource === 'id') {
                $namaId = $sourceName;
                $namaEn = $this->translator->translate($sourceName, 'en', 'id');
                if (empty($namaEn)) {
                    $translationFailed = true;
                    $namaEn = null;
                }
            } else {
                $namaEn = $sourceName;
                $namaId = $this->translator->translate($sourceName, 'id', 'en');
                if (empty($namaId)) {
                    $translationFailed = true;
                    $namaId = null;
                }
            }

            // Dimension localization
            $dimensiRaw = trim($request->dimensi);
            $dimensiId = str_ireplace(['Size ', 'Cotton'], ['Ukuran ', 'Katun'], $dimensiRaw);
            $dimensiEn = str_ireplace(['Ukuran ', 'Katun'], ['Size ', 'Cotton'], $dimensiRaw);

            Postgalery::create([
                'image'              => $optimized['filename'],
                'nama'               => $sourceName,
                'nama_id'            => $namaId,
                'nama_en'            => $namaEn,
                'dimensi'            => $dimensiRaw,
                'dimensi_id'         => $dimensiId,
                'dimensi_en'         => $dimensiEn,
                'link'               => $request->link,
                'translation_source' => $detectedSource,
                'translation_manual' => false,
            ]);

            if ($translationFailed) {
                return redirect()->route('postsgalery.index')->with([
                    'success' => __('flash.saved_success'),
                    'warning' => __('flash.translation_warning'),
                ]);
            }

            return redirect()->route('postsgalery.index')->with([
                'success' => __('flash.saved_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gallery image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function edit($id): View
    {
        $post = Postgalery::findOrFail($id);
        return view('editgalery', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image'      => 'nullable|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
            'nama'       => 'nullable|string|max:255',
            'nama_id'    => 'required_without:nama|nullable|string|max:255',
            'nama_en'    => 'required_without:nama|nullable|string|max:255',
            'dimensi'    => 'nullable|string|max:255',
            'dimensi_id' => 'required_without:dimensi|nullable|string|max:255',
            'dimensi_en' => 'nullable|string|max:255',
            'link'       => 'required|string',
        ]);

        $post = Postgalery::findOrFail($id);

        try {
            $oldImage = $post->image;
            $newImageUploaded = false;

            if ($request->hasFile('image')) {
                $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');
                $post->image = $optimized['filename'];
                $newImageUploaded = true;
            }

            // Handle dual bilingual input from edit form
            if ($request->filled('nama_id') || $request->filled('nama_en')) {
                $newId = trim($request->input('nama_id', ''));
                $newEn = trim($request->input('nama_en', ''));

                $idChanged = ($newId !== (string)$post->nama_id);
                $enChanged = ($newEn !== (string)$post->nama_en);

                $post->nama_id = $newId ?: null;
                $post->nama_en = $newEn ?: null;
                $post->nama    = $newId ?: ($newEn ?: $post->nama);

                if ($idChanged || $enChanged) {
                    $post->translation_manual = true;
                }
            } elseif ($request->filled('nama')) {
                $sourceText = trim($request->nama);
                $detectedSource = $this->translator->detectLanguage($sourceText);
                $post->nama = $sourceText;
                $post->translation_source = $detectedSource;

                if ($detectedSource === 'id') {
                    $post->nama_id = $sourceText;
                    $trans = $this->translator->translate($sourceText, 'en', 'id');
                    if (!empty($trans)) {
                        $post->nama_en = $trans;
                    }
                } else {
                    $post->nama_en = $sourceText;
                    $trans = $this->translator->translate($sourceText, 'id', 'en');
                    if (!empty($trans)) {
                        $post->nama_id = $trans;
                    }
                }
            }

            // Dimension update
            if ($request->filled('dimensi_id') || $request->filled('dimensi_en')) {
                $post->dimensi_id = trim($request->input('dimensi_id', ''));
                $post->dimensi_en = trim($request->input('dimensi_en', ''));
                $post->dimensi    = $post->dimensi_id ?: ($post->dimensi_en ?: $post->dimensi);
            } elseif ($request->filled('dimensi')) {
                $dimRaw = trim($request->dimensi);
                $post->dimensi    = $dimRaw;
                $post->dimensi_id = str_ireplace(['Size ', 'Cotton'], ['Ukuran ', 'Katun'], $dimRaw);
                $post->dimensi_en = str_ireplace(['Ukuran ', 'Katun'], ['Size ', 'Cotton'], $dimRaw);
            }

            $post->link = $request->link;
            $post->save();

            // Only delete old variants after new image and model have been successfully saved
            if ($newImageUploaded && !empty($oldImage) && $oldImage !== $post->image) {
                MediaPipeline::deleteVariants($oldImage, 'postsimg');
            }

            return redirect()->route('postsgalery.index')->with([
                'success' => __('flash.updated_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gallery image update optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Postgalery::findOrFail($id);
        $imageToDelete = $post->image;

        $post->delete();

        if (!empty($imageToDelete)) {
            try {
                MediaPipeline::deleteVariants($imageToDelete, 'postsimg');
            } catch (\Throwable $e) {
                Log::warning('Error deleting gallery image variants: ' . $e->getMessage());
            }
        }

        return redirect()->route('postsgalery.index')->with([
            'success' => __('flash.deleted_success'),
        ]);
    }
}

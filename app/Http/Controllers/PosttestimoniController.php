<?php

namespace App\Http\Controllers;

use App\Models\Posttestimoni;
use App\Services\MediaPipeline;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PosttestimoniController extends Controller
{
    public function index(): View
    {
        $posts = Posttestimoni::latest()->get();

        return view('posttestimoni', compact('posts'));
    }

    public function create(): View
    {
        return view('createtestimoni');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'required|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
        ]);

        try {
            $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');

            Posttestimoni::create([
                'image' => $optimized['filename'],
            ]);

            return redirect()->route('posttestimoni.index')->with([
                'success' => __('flash.saved_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Testimoni image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function edit($id): View
    {
        $post = Posttestimoni::findOrFail($id);
        return view('edittestimoni', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
        ]);

        $post = Posttestimoni::findOrFail($id);

        try {
            $oldImage = $post->image;
            $newImageUploaded = false;

            if ($request->hasFile('image')) {
                $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');
                $post->image = $optimized['filename'];
                $newImageUploaded = true;
            }

            $post->save();

            // Only delete old variants after new image and model have been successfully saved
            if ($newImageUploaded && !empty($oldImage) && $oldImage !== $post->image) {
                MediaPipeline::deleteVariants($oldImage, 'postsimg');
            }

            return redirect()->route('posttestimoni.index')->with([
                'success' => __('flash.updated_success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Testimoni image update optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => __('flash.image_error') . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Posttestimoni::findOrFail($id);
        $imageToDelete = $post->image;

        $post->delete();

        if (!empty($imageToDelete)) {
            try {
                MediaPipeline::deleteVariants($imageToDelete, 'postsimg');
            } catch (\Throwable $e) {
                Log::warning('Error deleting testimoni image variants: ' . $e->getMessage());
            }
        }

        return redirect()->route('posttestimoni.index')->with([
            'success' => __('flash.deleted_success'),
        ]);
    }
}

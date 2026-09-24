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

            return redirect()->route('posttestimoni.index')->with(['success' => 'Data Berhasil Disimpan & Gambar Dioptimasi Otomatis!']);
        } catch (\Throwable $e) {
            Log::error('Testimoni image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => 'Gagal memproses gambar: ' . $e->getMessage()]);
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
            'image' => 'required|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
        ]);

        $post = Posttestimoni::findOrFail($id);

        try {
            MediaPipeline::deleteVariants($post->image, 'postsimg');
            $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');
            $post->image = $optimized['filename'];
            $post->save();

            return redirect()->route('posttestimoni.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } catch (\Throwable $e) {
            Log::error('Testimoni image update optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => 'Gagal memproses gambar: ' . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Posttestimoni::findOrFail($id);

        try {
            MediaPipeline::deleteVariants($post->image, 'postsimg');
        } catch (\Throwable $e) {
            Log::warning('Error deleting testimoni image variants: ' . $e->getMessage());
        }

        $post->delete();

        return redirect()->route('posttestimoni.index')->with(['success' => 'Data Delete Successfully']);
    }
}

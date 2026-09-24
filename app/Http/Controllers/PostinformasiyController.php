<?php

namespace App\Http\Controllers;

use App\Models\Postinformasi;
use App\Services\MediaPipeline;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PostinformasiyController extends Controller
{
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

            Postinformasi::create([
                'image'     => $optimized['filename'],
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('postsinformasi.index')->with(['success' => 'Data Berhasil Disimpan & Gambar Dioptimasi Otomatis!']);
        } catch (\Throwable $e) {
            Log::error('Informasi image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => 'Gagal memproses gambar: ' . $e->getMessage()]);
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
            'image'     => 'nullable|file|mimes:jpeg,jpg,png,webp,avif|max:15360',
            'deskripsi' => 'required|string',
        ]);

        $post = Postinformasi::findOrFail($id);

        try {
            if ($request->hasFile('image')) {
                MediaPipeline::deleteVariants($post->image, 'postsimg');
                $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');
                $post->image = $optimized['filename'];
            }

            $post->deskripsi = $request->deskripsi;
            $post->save();

            return redirect()->route('postsinformasi.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } catch (\Throwable $e) {
            Log::error('Informasi image update optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => 'Gagal memproses gambar: ' . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Postinformasi::findOrFail($id);

        try {
            MediaPipeline::deleteVariants($post->image, 'postsimg');
        } catch (\Throwable $e) {
            Log::warning('Error deleting informasi image variants: ' . $e->getMessage());
        }

        $post->delete();

        return redirect()->route('postsinformasi.index')->with(['success' => 'Data Delete Successfully']);
    }
}

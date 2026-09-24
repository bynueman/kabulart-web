<?php

namespace App\Http\Controllers;

use App\Models\Postgalery;
use App\Services\MediaPipeline;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PostgaleryController extends Controller
{
    public function index(): View
    {
        $posts = Postgalery::latest()->get();

        //render view with posts
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
            // Automatic optimization pipeline
            $optimized = MediaPipeline::processUpload($request->file('image'), 'postsimg');

            Postgalery::create([
                'image'   => $optimized['filename'],
                'nama'    => $request->nama,
                'dimensi' => $request->dimensi,
                'link'    => $request->link,
            ]);

            return redirect()->route('postsgalery.index')->with(['success' => 'Data Berhasil Disimpan & Gambar Dioptimasi Otomatis!']);
        } catch (\Throwable $e) {
            Log::error('Gallery image upload optimization failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['image' => 'Gagal memproses gambar: ' . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $post = Postgalery::findOrFail($id);

        try {
            MediaPipeline::deleteVariants($post->image, 'postsimg');
        } catch (\Throwable $e) {
            Log::warning('Error deleting gallery image variants: ' . $e->getMessage());
        }

        $post->delete();

        return redirect()->route('postsgalery.index')->with(['success' => 'Data Delete Successfully']);
    }
}

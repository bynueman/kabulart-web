<?php

namespace App\Http\Controllers;

use App\Models\Postinformasi;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PostinformasiyController extends Controller
{
    //

    public function index(): View
    {
        //get post
        $posts = Postinformasi::latest()->get();

        //render view with posts
        return view('postinformasi', compact('posts'));
    }

    public function create():View
    {
        return view('createinformasi');
    }

    public function store(Request $request): RedirectResponse
    {
        //variabel from
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'deskripsi'     => 'required',
        ]);
        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/postsimg/', $image->hashName());

        //create post
        Postinformasi::create([
            'image'     => $image->hashName(),
            'deskripsi'     => $request->deskripsi,
        ]);

        //return redirect index
        return redirect()->route('postsinformasi.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id): RedirectResponse
    {
            //get post id:
            $post = Postinformasi::findOrFail($id);

            //delete img:
            Storage::delete('public/postsimg/'. $post->image);
            if (file_exists(public_path('storage/postsimg/' . $post->image))) {
                @unlink(public_path('storage/postsimg/' . $post->image));
            }
            
            //delete post
            $post->delete();

            //redirect to index
            return redirect()->route('postsinformasi.index')->with(['success' => 'Data Delete Successfully']);
    }
}

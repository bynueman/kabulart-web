<?php

namespace App\Http\Controllers;

use App\Models\Postgalery;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PostgaleryController extends Controller
{
    //

    public function index(): View
    {
        $posts = Postgalery::latest()->get();

        //render view with posts
        return view('postgallery', compact('posts'));
    }

    public function create():View
    {
        return view('creategalery');
    }

    public function store(Request $request): RedirectResponse
    {
        //variabel from
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,jpg,png|max:4048',
            'nama'     => 'required',
            'dimensi'     => 'required',
            'link'      => 'required',
        ]);
        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/postsimg/', $image->hashName());

        //create post
        Postgalery::create([
            'image'     => $image->hashName(),
            'nama'          => $request->nama,
            'dimensi'       => $request->dimensi,
            'link'      => $request->link,
        ]);

        return redirect()->route('postsgalery.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id): RedirectResponse
    {
        $post = Postgalery::findOrFail($id);

        Storage::delete('public/postsimg/'. $post->image);
        if (file_exists(public_path('storage/postsimg/' . $post->image))) {
            @unlink(public_path('storage/postsimg/' . $post->image));
        }
            
        $post->delete();

        return redirect()->route('postsgalery.index')->with(['success' => 'Data Delete Successfully']);
    }
}

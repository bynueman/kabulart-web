<?php

namespace App\Http\Controllers;

use App\Models\Posttestimoni;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PosttestimoniController extends Controller
{
    public function index(): View
    {
        //get post
        $posts = Posttestimoni::latest()->get();

        //render view with posts
        return view('posttestimoni', compact('posts'));
    }

    public function create():View
    {
        return view('createtestimoni');
    }

    public function store(Request $request): RedirectResponse
    {
        //variabel from
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/postsimg/', $image->hashName());

        //create post
        Posttestimoni::create([
            'image'     => $image->hashName(),
        ]);

        //return redirect index
        return redirect()->route('posttestimoni.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id): RedirectResponse
    {
            //get post id:
            $post = Posttestimoni::findOrFail($id);

            //delete img:
            Storage::delete('public/postsimg/'. $post->image);
            if (file_exists(public_path('storage/postsimg/' . $post->image))) {
                @unlink(public_path('storage/postsimg/' . $post->image));
            }
            
            //delete post
            $post->delete();

            //redirect to index
            return redirect()->route('posttestimoni.index')->with(['success' => 'Data Delete Successfully']);
    }
}

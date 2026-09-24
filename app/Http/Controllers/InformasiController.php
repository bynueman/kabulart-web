<?php

namespace App\Http\Controllers;

use App\Models\Postinformasi;
use Illuminate\View\View;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index(): View
    {
        //get post
        $posts = Postinformasi::latest()->get();

        //render view with posts
        return view('informasi', compact('posts'));
    }
}

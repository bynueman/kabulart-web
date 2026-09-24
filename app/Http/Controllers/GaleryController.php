<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Postgalery;
use Illuminate\Http\Request;

class GaleryController extends Controller
{
    public function index(): View
    {
        $posts = Postgalery::latest()->get();

        return view('galery', compact('posts'));
    }
}

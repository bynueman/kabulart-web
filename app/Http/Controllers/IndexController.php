<?php

namespace App\Http\Controllers;

use App\Models\Postgalery;
use App\Models\Postinformasi;
use Illuminate\View\View;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(): View
    {   
        $posts1 = Postinformasi::latest()->take(2)->get();
        $posts2 = Postgalery::latest()->take(6)->get();

        return view('index', compact('posts1', 'posts2'));
    }
}

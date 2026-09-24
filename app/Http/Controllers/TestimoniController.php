<?php

namespace App\Http\Controllers;

use App\Models\Posttestimoni;
use Illuminate\View\View;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function index(): View
    {
        $posts = Posttestimoni::latest()->get();

        return view('testimoni', compact('posts'));
    }
}

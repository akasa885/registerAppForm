<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Link;

class IndexController extends Controller
{
    public function index()
    {
        $link = Link::orderBy('created_at', 'DESC')->get();
        return view('pages.home.home', ['link' => $link]);
    }   
}

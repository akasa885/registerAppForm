<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Link;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $link = Link::where(function ($query) {
            $query->IsLinkViewable();
        })->orderBy('id', 'DESC')->paginate(12);
        if ($request->ajax()) {
            $view = view('pages.home.data.list-link', ['link' => $link])->render();

            return response()->json(['html' => $view]);
        }

        return view('pages.home.home', ['link' => $link]);
    }   
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:50',
            'color'=>'required|string|max:7',
        ]);
        Tag::create([
            'name'=>$request->name,
            'color'=>$request->color,
        ]);
        return back()->with('succes', 'Etiket başarıyla eklendi');


    }
    public function destroy(Tag $tag){
        $tag->delete();
        return back()->with('succes','Etiket başarıyla silindi');
    }
}

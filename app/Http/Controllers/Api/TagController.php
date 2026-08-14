<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index(){
        $tags=Tag::all();
        return response()->json([
'success'=>true,
'data'=>$tags
        ],200);
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'color' => $request->color,
        ]);
        return response()->json([
            'success'=>true,
            'message'=>'Etiket başarıyla eklendi.',
            'data'=>$tag
        ],201);


    }
    public function destroy(Tag $tag){
        $tag->delete();
        return response()->json([
            'success'=>true,
            'message'=> 'Etiket başarıyla silindi'
        ],200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    public function store(Request $request,$taskId){
        $request->validate([
            'comment'=>'required|string|max:1000',
        ]);

        TaskComment::create([
            'task_id'=>$taskId,
            'user_id' => Auth::id(),
            'comment'=> $request->comment,
        ]);
        return back()->with('success','Yorum eklendi.');

    }
    public function destroy($id){
        $comment=TaskComment::findOrFail($id);
        $comment->delete();
        return back()->with('success','Yorum silindi.');
    }
}

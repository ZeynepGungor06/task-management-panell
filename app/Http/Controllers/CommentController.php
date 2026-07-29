<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\TaskCompletedException;
use App\Models\Task;


class CommentController extends Controller
{
    public function store(Request $request,$taskId){
        $task = \App\Models\Task::findOrFail($taskId);
        if ($task->due_date && $task->due_date->isPast()) {
        abort(403, 'Bu görevin teslim tarihi geçtiği için işlem yapılamaz.');
    }
        $task=Task::findOrFail($taskId);
        if($task->is_completed){
            throw new TaskCompletedException();
        }
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
    public function toggleSpam(Request $request,$id){
        if($request->user()->role !== 'admin'){
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır');
        }
        $comment=\App\Models\TaskComment::findOrFail($id);
        $comment->is_spam=!$comment->is_spam;
        $comment->save();

        return back();
    }
    public function destroy($id){
        $comment=TaskComment::findOrFail($id);
        $comment->delete();
        return back()->with('success','Yorum silindi.');
    }
}

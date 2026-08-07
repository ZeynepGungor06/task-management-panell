<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskComment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request,$taskId){
        $task = Task::findOrFail($taskId);
        if ($task->due_date && $task->due_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu görevin teslim tarihi geçtiği için işlem yapılamaz.'
            ], 403);
        }

        if ($task->is_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Tamamlanmış görevlere yorum yapılamaz.'
            ], 403);
        }
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = TaskComment::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Yorum başarıyla eklendi',
            'data'=>$comment        ],201);
    }

    public function toggleSpam(Request $request, $id)
    {
        if ($request->user()->role !== 'ADMİN') {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz bulunmamaktadır.'
            ], 403);
        }

        $comment = TaskComment::findOrFail($id);
        $comment->is_spam = !$comment->is_spam;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Yorum spam durumu güncellendi.',
            'data' => $comment
        ], 200);
    }
    public function destroy($id)
    {
        $comment = TaskComment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Yorum başarıyla silindi.'
        ], 200);
    }
}

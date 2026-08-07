<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\TaskFile;
use App\Models\Task; // Task modelini ekledik
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFileRequest;

class DocumentController extends Controller
{
    public function store(StoreFileRequest $request,$taskId){
        $task = Task::findOrFail($taskId);
        
        if ($task->due_date && $task->due_date->isPast()) {
            
            return response()->json([
                'success' => false,
                'message' => 'Bu görevin teslim tarihi geçtiği için işlem yapılamaz.'
            ], 403);
        }
      
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('task_files', 'public');

        $taskFile = TaskFile::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'file_path' => $path,
            'original_name' => $originalName,
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Dosya başarıyla yüklendi',
            'data'=>$taskFile
        ],201);
    }
    public function download($id){
        $file = TaskFile::findOrFail($id);
        return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);
    }
    public function destroy($id){
        $file = TaskFile::findOrFail($id);
        
        if(Storage::disk('public')->exists($file->file_path)){
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();
        return response()->json([
            'success' => true,
            'message' => 'Dosya başarıyla silindi.'
        ], 200);
    }
}

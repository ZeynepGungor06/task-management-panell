<?php

namespace App\Http\Controllers;

use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFileRequest;
class DocumentController extends Controller
{
    public function store(StoreFileRequest $request, $taskId){
        $task = \App\Models\Task::findOrFail($taskId);
        if ($task->due_date && $task->due_date->isPast()) {
        abort(403, 'Bu görevin teslim tarihi geçtiği için işlem yapılamaz.');
    }
      
        $file=$request->file('file');
        $originalName=$file->getClientOriginalName();
        $path=$file->store('task_files','public');

        TaskFile::create([
            'task_id'=>$taskId,
           'user_id' => Auth::id(),
            'file_path'=>$path,
            'original_name'=> $originalName,
        ]);
        return back()->with('success','Dosya başarıyla yüklendi');


    }
    public function download($id){
        $file=TaskFile::findOrFail($id);
       return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);

    }
    public function destroy($id){
        $file=TaskFile::findOrFail($id);
        if(Storage::disk('public')->exists($file->file_path)){
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();
        return back()->with('succes','Dosya silindi');
    }
}

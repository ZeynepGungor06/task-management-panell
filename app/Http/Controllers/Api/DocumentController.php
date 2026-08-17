<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use App\Models\TaskFile;
use App\Models\Task; // Task modelini ekledik
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
      DB::beginTransaction();
      try {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('task_files', 'public');

        $taskFile = TaskFile::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'file_path' => $path,
            'original_name' => $originalName,
        ]);
        DB::commit();

        return response()->json([
            'success'=>true,
            'message'=>'Dosya başarıyla yüklendi',
            'data'=>$taskFile
        ],201);} catch (\Exception $e) {
            if(isset($path) && Storage::disk('public')->exists($path)){
                Storage::disk('public')->delete($path);
            }
            Log::error('API Görev dosyası yükleme hatası: ' .$e->getMessage());
            return response()->json([
                'success'=>false,
                'message'=> 'Dosya yüklenirken sistemsel bir hata oluştu.'
            ],500);
        }
    }
    public function download($id){
        $file = TaskFile::findOrFail($id);
        return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);
    }
    public function destroy($id){
        $file = TaskFile::findOrFail($id);
        DB::beginTransaction();
        try {
            $file->delete();
        
        if(Storage::disk('public')->exists($file->file_path)){
            Storage::disk('public')->delete($file->file_path);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Dosya başarıyla silindi.'
        ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Görev dosyası silme hatası: ' .$e->getMessage());
            return response()->json([
                'success'=> false,
                'message'=> 'Dosya silinirken bir hata oluştu.'
            ],500);
        }
    }
}

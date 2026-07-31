<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Veritabanına dışarıdan eklenebilecek sütunları belirtiyoruz
    protected $fillable = ['user_id', 'title', 'is_completed','priority','due_date'];

    protected $casts=['due_date'=>'datetime',];

    // Görevin kime ait olduğu (User bağlantısı) - Bunu da ekleyelim, garanti olsun
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Görevin dosyaları
    public function files() {
        return $this->hasMany(TaskFile::class);
    }

    // Görevin yorumları
    public function comments() {
        return $this->hasMany(TaskComment::class);
    }
    public function tags(){
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Veritabanına dışarıdan eklenebilecek sütunları belirtiyoruz
    protected $fillable = ['user_id', 'title', 'is_completed'];
}
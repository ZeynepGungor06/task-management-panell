<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    
    public function run(): void
    {
        Tag::create(['name'=>'backend','color'=>'#0d6efd']);
        Tag::create(['name'=> 'frontend','color'=> '#20c997']);
        Tag::create(['name'=> 'Acil','color'=> '#dc3545']);
        Tag::create(['name'=> 'Toplantı','color'=> '#ffc107']);

    }
}

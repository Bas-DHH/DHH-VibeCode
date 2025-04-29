<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'input_fields',
        'is_active'
    ];

    protected $casts = [
        'input_fields' => 'array',
        'is_active' => 'boolean'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

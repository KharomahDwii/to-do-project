<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

protected $fillable = ['title', 'description', 'completed', 'reminder_at', 'notified'];

protected $casts = [
    'reminder_at' => 'datetime',
    'completed' => 'boolean',
    'notified' => 'boolean',
];
}
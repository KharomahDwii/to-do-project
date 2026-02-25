<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'completed',
        'reminder_at',
        'metadata',
        'media_path'
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
<<<<<<< HEAD

public function category()
{
    return $this->belongsTo(Category::class);
}

=======
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
}
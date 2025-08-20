<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'order',
        'user_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // get the user with tasks.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //    query to include specific task with status
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    //    query to include specific task with priority
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    //    query to order tasks by their order field.
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}

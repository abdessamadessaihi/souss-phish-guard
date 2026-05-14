<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'content_url',
        'quiz_data',
        'duration_minutes',
        'difficulty',
        'points_reward',
        'is_active',
        'locale',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'training_user')->withPivot('status', 'score', 'attempts', 'completed_at')->withTimestamps();
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'template',
        'subject',
        'body',
        'from_name',
        'from_email',
        'landing_url',
        'tracking_token',
        'status',
        'scheduled_at',
        'completed_at',
        'targets_count',
        'opened_count',
        'clicked_count',
        'submitted_count',
        'reported_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function results()
    {
        return $this->hasMany(SimulationResult::class);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->targets_count == 0)
            return 0;
        return round(($this->clicked_count / $this->targets_count) * 100, 1);
    }
}
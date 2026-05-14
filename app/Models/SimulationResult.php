<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationResult extends Model
{
    protected $fillable = [
        'simulation_id',
        'user_id',
        'unique_token',
        'email_opened',
        'link_clicked',
        'data_submitted',
        'reported_phish',
        'opened_at',
        'clicked_at',
        'submitted_at',
        'reported_at',
        'user_ip',
        'user_agent',
        'outcome',
    ];

    protected $casts = [
        'email_opened' => 'boolean',
        'link_clicked' => 'boolean',
        'data_submitted' => 'boolean',
        'reported_phish' => 'boolean',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reported_at' => 'datetime',
    ];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
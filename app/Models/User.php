<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'vigilance_score',
        'reports_count',
        'simulations_passed',
        'simulations_failed',
        'is_active',
        'avatar',
        'locale',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function phishReports()
    {
        return $this->hasMany(PhishReport::class);
    }
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_user')->withPivot('status', 'score', 'attempts', 'completed_at')->withTimestamps();
    }
    public function simulationResults()
    {
        return $this->hasMany(SimulationResult::class);
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getVigilanceLevelAttribute(): string
    {
        return match (true) {
            $this->vigilance_score >= 80 => 'Expert',
            $this->vigilance_score >= 60 => 'Avancé',
            $this->vigilance_score >= 40 => 'Intermédiaire',
            default => 'Débutant',
        };
    }
}
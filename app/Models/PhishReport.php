<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhishReport extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'content',
        'subject',
        'sender_email',
        'sender_ip',
        'email_headers',
        'ai_analysis',
        'ai_risk_score',
        'virustotal_result',
        'status',
        'severity',
        'admin_feedback',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'virustotal_result' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getRiskBadgeAttribute(): string
    {
        return match (true) {
            $this->ai_risk_score >= 80 => 'critical',
            $this->ai_risk_score >= 60 => 'high',
            $this->ai_risk_score >= 40 => 'medium',
            default => 'low',
        };
    }
}
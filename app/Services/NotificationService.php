<?php
namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function create(
        int $userId,
        string $type,
        string $title,
        string $body = '',
        string $link = '#',
        string $icon = 'bi-bell-fill',
        string $color = 'green'
    ): void {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body ?: '',   // garantit jamais null
            'link' => $link,
            'icon' => $icon,
            'color' => $color,
        ]);
    }

    public static function notifyAdmins(
        string $type,
        string $title,
        string $body = '',
        string $link = '#',
        string $icon = 'bi-bell-fill',
        string $color = 'red'
    ): void {
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            self::create($admin->id, $type, $title, $body, $link, $icon, $color);
        }
    }

    public static function message(User $receiver, User $sender, string $preview = ''): void
    {
        self::create(
            $receiver->id,
            'message',
            'Nouveau message de ' . $sender->name,
            $preview ?: 'Vous avez reçu un nouveau message.',
            $receiver->isAdmin() ? '/admin/messages' : '/user/messages',
            'bi-chat-dots-fill',
            'green'
        );
    }

    public static function reportSubmitted(int $adminId, string $userName, int $reportId, int $score): void
    {
        self::create(
            $adminId,
            'new_report',
            "Nouveau signalement #{$reportId}",
            "{$userName} a soumis un signalement — Score IA: {$score}%",
            "/admin/reports/{$reportId}",
            'bi-flag-fill',
            $score >= 70 ? 'red' : 'amber'
        );
    }

    public static function reportReviewed(int $userId, int $reportId, string $status, string $feedback = ''): void
    {
        self::create(
            $userId,
            'report_reviewed',
            "Signalement #{$reportId} traité",
            $feedback ?: "Statut mis à jour : {$status}",
            "/user/reports/{$reportId}",
            'bi-shield-check',
            'green'
        );
    }

    public static function forensicAnalysis(User $admin, string $riskLevel): void
    {
        if (!in_array($riskLevel, ['high', 'critical']))
            return;
        self::create(
            $admin->id,
            'system',
            'Forensic : risque ' . strtoupper($riskLevel),
            'Analyse forensic headers terminée.',
            '/admin/forensic',
            'bi-cpu-fill',
            $riskLevel === 'critical' ? 'red' : 'amber'
        );
    }
}
<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Training;
use App\Models\PhishReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // ── ADMIN ──
        $admin = User::create([
            'name' => 'Admin SPG',
            'email' => 'admin@spg.ma',
            'password' => Hash::make('Admin@SPG2024!'),
            'role' => 'admin',
            'department' => 'Security',
            'vigilance_score' => 100,
            'is_active' => true,
        ]);

        // ── USERS TEST ──
        $users = [];
        $departments = ['IT', 'RH', 'Finance', 'Commercial', 'Direction'];
        for ($i = 1; $i <= 5; $i++) {
            $users[] = User::create([
                'name' => "Employé Test $i",
                'email' => "user$i@spg.ma",
                'password' => Hash::make('User@SPG2024!'),
                'role' => 'user',
                'department' => $departments[$i - 1],
                'vigilance_score' => rand(10, 85),
                'reports_count' => rand(0, 10),
                'simulations_passed' => rand(0, 5),
                'simulations_failed' => rand(0, 3),
                'is_active' => true,
            ]);
        }

        // ── MODULES DE FORMATION ──
        $trainings = [
            [
                'title' => 'Reconnaître un email de phishing',
                'description' => "Le phishing est l'une des cyberattaques les plus répandues. Dans ce module, vous apprendrez à identifier les 7 signaux d'alerte principaux d'un email malveillant : expéditeur falsifié, urgence artificielle, liens trompeurs, pièces jointes dangereuses, fautes d'orthographe, demandes d'identifiants et usurpation de marque.",
                'type' => 'video',
                'content_url' => 'https://www.youtube.com/embed/XBkzBrXlle0',
                'duration_minutes' => 12,
                'difficulty' => 'beginner',
                'points_reward' => 20,
                'is_active' => true,
                'quiz_data' => json_encode([
                    ['question' => "Quel est le premier signe d'un email de phishing ?", 'options' => ["Un logo coloré", "Une adresse expéditeur suspecte", "Un long texte", "Une signature HTML"], 'answer' => 1],
                    ['question' => "Que faire si un email vous demande votre mot de passe ?", 'options' => ["Le fournir si l'expéditeur semble légit", "Ignorer", "Signaler immédiatement sur SPG", "Répondre poliment"], 'answer' => 2],
                    ['question' => "L'urgence dans un email est généralement...", 'options' => ["Un signe de professionnalisme", "Une technique de manipulation", "Normale dans les emails pro", "Un bug d'affichage"], 'answer' => 1],
                    ['question' => "Un email de phishing peut venir de...", 'options' => ["Uniquement des inconnus", "N'importe qui, même des contacts connus", "Uniquement des pays étrangers", "Uniquement des adresses suspectes"], 'answer' => 1],
                ]),
            ],
            [
                'title' => "Ingénierie sociale : décoder les manipulations",
                'description' => "L'ingénierie sociale exploite la psychologie humaine plutôt que les failles techniques. Les attaquants utilisent la peur, l'autorité, l'urgence et la confiance pour vous piéger. Ce module vous apprend à reconnaître les techniques de manipulation : pretexting, baiting, quid pro quo, tailgating et spear phishing ciblé.",
                'type' => 'article',
                'content_url' => null,
                'duration_minutes' => 18,
                'difficulty' => 'intermediate',
                'points_reward' => 30,
                'is_active' => true,
                'quiz_data' => json_encode([
                    ['question' => "L'ingénierie sociale exploite principalement...", 'options' => ["Les failles logicielles", "La psychologie humaine", "Le réseau WiFi", "Les mots de passe courts"], 'answer' => 1],
                    ['question' => "Le 'pretexting' consiste à...", 'options' => ["Envoyer des fichiers infectés", "Créer un faux scénario crédible", "Intercepter le trafic réseau", "Brute-forcer des mots de passe"], 'answer' => 1],
                    ['question' => "Le spear phishing se distingue car il est...", 'options' => ["Envoyé en masse", "Ciblé et personnalisé", "Uniquement par SMS", "Toujours détectable"], 'answer' => 1],
                    ['question' => "Face à une demande urgente d'un 'directeur' inconnu, vous devez...", 'options' => ["Exécuter immédiatement", "Vérifier via un autre canal", "Ignorer", "Transférer à un collègue"], 'answer' => 1],
                ]),
            ],
            [
                'title' => 'Analyser une URL malveillante',
                'description' => "Les URLs malveillantes sont conçues pour tromper visuellement. Apprenez à analyser la structure d'une URL, détecter le typosquatting, identifier les domaines suspects (.tk, .ml, .cf), comprendre les redirections cachées, et utiliser les outils de vérification comme VirusTotal et l'Analyseur IA SPG.",
                'type' => 'quiz',
                'content_url' => null,
                'duration_minutes' => 10,
                'difficulty' => 'intermediate',
                'points_reward' => 25,
                'is_active' => true,
                'quiz_data' => json_encode([
                    ['question' => "Laquelle est une URL suspecte ?", 'options' => ["https://microsoft.com", "https://micros0ft-security.tk", "https://google.com", "https://apple.com"], 'answer' => 1],
                    ['question' => "HTTPS garantit-il qu'un site est sûr ?", 'options' => ["Oui toujours", "Non, pas forcément", "Oui si cadenas vert", "Uniquement pour les banques"], 'answer' => 1],
                    ['question' => "Le typosquatting est...", 'options' => ["Une faute de frappe innocente", "Un domaine imitant une marque connue", "Un virus", "Un type de cookie"], 'answer' => 1],
                    ['question' => "Une URL avec une adresse IP directe (ex: http://185.1.2.3/login) est...", 'options' => ["Normale", "Très suspecte", "Sécurisée", "Impossible"], 'answer' => 1],
                    ['question' => "Avant de cliquer un lien inconnu, vous devez...", 'options' => ["Cliquer rapidement", "Le copier dans l'Analyseur IA SPG", "L'envoyer à un ami", "Attendre 24h"], 'answer' => 1],
                ]),
            ],
            [
                'title' => 'Sécuriser ses mots de passe & 2FA',
                'description' => "80% des violations de données impliquent des mots de passe compromis. Ce module couvre : création de mots de passe robustes (phrase de passe), utilisation d'un gestionnaire de mots de passe, activation de la double authentification (2FA/MFA), et bonnes pratiques pour éviter la réutilisation.",
                'type' => 'video',
                'content_url' => 'https://www.youtube.com/embed/aEmXedCCRaE',
                'duration_minutes' => 15,
                'difficulty' => 'beginner',
                'points_reward' => 20,
                'is_active' => true,
                'quiz_data' => json_encode([
                    ['question' => "Un bon mot de passe doit avoir au minimum...", 'options' => ["6 caractères", "8 caractères", "12 caractères", "4 caractères"], 'answer' => 2],
                    ['question' => "La 2FA protège même si...", 'options' => ["Votre mot de passe est fort", "Votre mot de passe est volé", "Votre email est sécurisé", "Vous utilisez un VPN"], 'answer' => 1],
                    ['question' => "Un gestionnaire de mots de passe est...", 'options' => ["Dangereux", "Utile uniquement en entreprise", "Essentiel et recommandé", "Trop compliqué"], 'answer' => 2],
                    ['question' => "Réutiliser le même mot de passe est...", 'options' => ["Pratique et sans risque", "Très risqué", "Acceptable si complexe", "Conseillé pour s'en souvenir"], 'answer' => 1],
                ]),
            ],
            [
                'title' => 'Simulation & Réponse aux incidents',
                'description' => "Que faire si vous tombez dans le piège d'un phishing ? Ce module pratique vous apprend la procédure de réponse : signalement immédiat, changement de mots de passe, notification de l'équipe IT, analyse forensique basique, et comment éviter de répéter l'erreur. Incluant les bonnes pratiques SPG.",
                'type' => 'quiz',
                'content_url' => null,
                'duration_minutes' => 8,
                'difficulty' => 'advanced',
                'points_reward' => 40,
                'is_active' => true,
                'quiz_data' => json_encode([
                    ['question' => "Si vous avez cliqué un lien phishing, votre 1ère action est...", 'options' => ["Attendre", "Signaler sur SPG ET changer vos mots de passe", "Fermer le navigateur", "Redémarrer le PC"], 'answer' => 1],
                    ['question' => "Après un incident phishing, vous devez notifier...", 'options' => ["Personne", "Votre équipe IT/sécurité", "Vos amis", "Les médias"], 'answer' => 1],
                    ['question' => "SPG vous permet de signaler via...", 'options' => ["Email uniquement", "Le module Signalements", "Twitter", "Appel téléphonique"], 'answer' => 1],
                    ['question' => "La meilleure défense contre le phishing est...", 'options' => ["Un antivirus", "La vigilance + formation continue", "Un pare-feu", "Changer d'email"], 'answer' => 1],
                ]),
            ],
        ];

        foreach ($trainings as $t) {
            Training::create($t);
        }

        // ── SIGNALEMENTS TEST ──
        $reportData = [
            ['type' => 'url', 'content' => 'http://micros0ft-login.tk/account-verify', 'sender_email' => 'security@micros0ft.tk', 'status' => 'confirmed_phish', 'severity' => 'critical', 'ai_risk_score' => 95],
            ['type' => 'email', 'content' => 'Votre compte bancaire a été suspendu. Cliquez ici pour le réactiver immédiatement.', 'sender_email' => 'alertes@cih-banque-secure.com', 'status' => 'confirmed_phish', 'severity' => 'high', 'ai_risk_score' => 82],
            ['type' => 'url', 'content' => 'https://docs.google.com/forms/fakeid', 'status' => 'pending', 'severity' => 'medium', 'ai_risk_score' => 45],
        ];

        foreach ($reportData as $i => $r) {
            PhishReport::create(array_merge($r, [
                'user_id' => $users[$i % count($users)]->id,
                'ai_analysis' => 'Analyse IA : URL suspecte détectée avec score de risque élevé. Domaine enregistré récemment, utilisation de caractères de substitution.',
            ]));
        }
    }
}
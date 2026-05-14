@extends('layouts.app')
@section('title', $training->title)
@section('page-title', 'FORMATION')

@section('content')
    <div class="fade-in" >
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">SPG / <span>Formation</span> / {{ Str::limit($training->title, 40) }}</div>
                <div class="page-header-title">{{ $training->title }}</div>
            </div>
            <a href="{{ route('user.training.index') }}" class="btn-cyber btn-cyber-warning">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Méta -->
        <div class="row g-3 mb-4">
            @foreach([
                    ['icon' => 'bi-clock-fill', 'color' => 'green', 'val' => $training->duration_minutes . ' min', 'lbl' => 'DURÉE'],
                    ['icon' => 'bi-bar-chart-fill', 'color' => 'amber', 'val' => ucfirst($training->difficulty), 'lbl' => 'NIVEAU'],
                    ['icon' => 'bi-star-fill', 'color' => 'amber', 'val' => '+' . $training->points_reward . ' pts', 'lbl' => 'RÉCOMPENSE'],
                    ['icon' => 'bi-tag-fill', 'color' => 'blue', 'val' => strtoupper($training->type), 'lbl' => 'TYPE'],
                ] as $info)
                <div class="col-md-3 col-6">
                    <div class="stat-card" style="padding:14px;">
                        <div class="stat-icon {{ $info['color'] }}" style="width:36px;height:36px;font-size:14px;">
                            <i class="bi {{ $info['icon'] }}"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--{{ $info['color'] === 'green' ? 'emerald' : ($info['color'] === 'amber' ? 'amber' : 'sky') }});">{{ $info['val'] }}</div>
                            <div class="stat-label" style="font-size:9px;">{{ $info['lbl'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Vidéo principale -->
        @if($training->type === 'video' && $training->content_url)
            <div class="cyber-card mb-4">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-play-circle-fill"></i> VIDÉO DE FORMATION</div>
                    <span class="cyber-badge badge-info">{{ $training->duration_minutes }} min</span>
                </div>
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:var(--radius);background:#000;">
                    <iframe
                        src="{{ $training->content_url }}?rel=0&modestbranding=1"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;"
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                    </iframe>
                </div>
                <div style="margin-top:12px;padding:10px 14px;background:var(--bg-input);border-radius:var(--radius);font-size:12px;color:var(--text-secondary);">
                    <i class="bi bi-info-circle"></i> Regardez la vidéo complète avant de répondre au quiz ci-dessous.
                </div>
            </div>
        @endif

        <!-- Vidéos supplémentaires selon le type de formation -->
        @php
            $extraVideos = match ($training->id % 5) {
                1 => [
                    ['title' => 'Comment identifier un email de phishing', 'url' => 'https://www.youtube.com/embed/XBkzBrXlle0', 'duration' => '8 min'],
                    ['title' => 'Les 5 techniques de phishing les plus courantes', 'url' => 'https://www.youtube.com/embed/Y7zNlEMDmI4', 'duration' => '6 min'],
                ],
                2 => [
                    ['title' => "L'ingénierie sociale expliquée", 'url' => 'https://youtu.be/3vfkCq52Qok?si=CnPLubIAzXj6c658', 'duration' => '9 min'],
                ],
                3 => [
                    ['title' => 'Analyser une URL suspecte', 'url' => 'https://www.youtube.com/watch?v=iRf375BUIww', 'duration' => '7 min'],
                ],
                4 => [
                    ['title' => 'Créer un mot de passe fort', 'url' => 'https://youtu.be/-OI8VPOgWRc?si=VsYTxe94SqdV6J0k', 'duration' => '5 min'],
                ],
                default => []
            };
        @endphp

        @if(count($extraVideos) > 0)
            <div class="cyber-card mb-4">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-collection-play-fill"></i> VIDÉOS COMPLÉMENTAIRES</div>
                    <span class="cyber-badge badge-pending">{{ count($extraVideos) }} vidéo(s)</span>
                </div>
                <div class="row g-3">
                    @foreach($extraVideos as $vid)
                        <div class="col-md-6">
                            <div style="border:1px solid var(--border-subtle);border-radius:var(--radius);overflow:hidden;">
                                <div style="position:relative;padding-bottom:56.25%;height:0;background:#000;">
                                    <iframe
                                        src="{{ $vid['url'] }}?rel=0&modestbranding=1"
                                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <div style="padding:12px;background:var(--bg-elevated);">
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $vid['title'] }}</div>
                                    <div style="font-size:11px;color:var(--text-secondary);margin-top:3px;">
                                        <i class="bi bi-clock"></i> {{ $vid['duration'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Contenu textuel -->
        <div class="cyber-card mb-4">
            <div class="cyber-card-header">
                <div class="cyber-card-title"><i class="bi bi-file-text-fill"></i> CONTENU DU MODULE</div>
            </div>

            <div style="font-size:14px;line-height:1.9;color:var(--text-primary);margin-bottom:24px;">
                {{ $training->description }}
            </div>

            @php
                $contentBlocks = match ((int) $training->id) {
                    1 => [
                        ['icon' => '🎯', 'title' => 'Qu\'est-ce que le phishing ?', 'text' => 'Le phishing (hameçonnage) est une technique de fraude où un attaquant se fait passer pour une entité de confiance (banque, Microsoft, DHL...) afin de voler vos identifiants, données personnelles ou financières.'],
                        ['icon' => '🚨', 'title' => 'Les 7 signaux d\'alerte', 'text' => "1. Urgence artificielle (\"Votre compte sera bloqué dans 24h\")\n2. Expéditeur suspect (domaine différent de l'organisation)\n3. Liens trompeurs (survol ≠ texte affiché)\n4. Fautes d'orthographe et de grammaire\n5. Pièces jointes inattendues (.exe, .zip, .docm)\n6. Demande de mot de passe ou de données sensibles\n7. Offres trop belles pour être vraies"],
                        ['icon' => '✅', 'title' => 'Que faire face à un email suspect ?', 'text' => "Ne cliquez sur aucun lien. N'ouvrez pas les pièces jointes. Ne fournissez jamais vos identifiants. Signalez immédiatement via la plateforme SPG. Contactez votre équipe IT si vous avez un doute."],
                        ['icon' => '📊', 'title' => 'Statistiques 2025', 'text' => '91% des cyberattaques commencent par un email de phishing. 1 employé sur 3 clique sur un lien malveillant lors des tests. Le coût moyen d\'une violation de données liée au phishing est de 4,65 millions de dollars.'],
                    ],
                    2 => [
                        ['icon' => '🧠', 'title' => 'Qu\'est-ce que l\'ingénierie sociale ?', 'text' => 'L\'ingénierie sociale exploite la psychologie humaine plutôt que les failles techniques. Les attaquants manipulent la confiance, la peur, l\'urgence et l\'autorité pour obtenir ce qu\'ils veulent.'],
                        ['icon' => '🎭', 'title' => 'Techniques principales', 'text' => "Pretexting : création d'un faux scénario crédible\nBaiting : appât physique (clé USB piégée)\nQuid pro quo : échange de service contre information\nSpear phishing : attaque ciblée et personnalisée\nVishing : phishing par téléphone\nSmishing : phishing par SMS"],
                        ['icon' => '🛡️', 'title' => 'Comment se protéger ?', 'text' => "Vérifiez toujours l'identité de votre interlocuteur via un autre canal. Ne partagez jamais d'informations sensibles sous pression. Adoptez la politique du \"zéro confiance\". Formez-vous régulièrement sur les nouvelles techniques."],
                    ],
                    3 => [
                        ['icon' => '🔗', 'title' => 'Anatomie d\'une URL malveillante', 'text' => "Une URL légitime : https://www.microsoft.com/fr-fr/security\nUne URL piégée : https://microssoft-security.tk/fr-fr/account\n\nDifférences : domaine mal orthographié, extension suspecte (.tk, .ml, .ga), sous-domaine trompeur."],
                        ['icon' => '🔍', 'title' => 'Comment vérifier un lien ?', 'text' => "1. Survolez le lien sans cliquer pour voir l'URL réelle\n2. Copiez l'URL et analysez-la dans l'Analyseur IA SPG\n3. Vérifiez le domaine principal (partie avant .com/.fr)\n4. Méfiez-vous des redirecteurs (bit.ly, tinyurl)\n5. Utilisez VirusTotal.com pour vérifier"],
                        ['icon' => '⚠️', 'title' => 'Le mythe du cadenas HTTPS', 'text' => 'HTTPS ne garantit PAS qu\'un site est sécurisé. Les pirates obtiennent facilement des certificats SSL gratuits. Le cadenas signifie uniquement que la communication est chiffrée, pas que le site est légitime.'],
                    ],
                    4 => [
                        ['icon' => '🔐', 'title' => 'Règles d\'or pour les mots de passe', 'text' => "Minimum 12 caractères (16+ recommandé)\nMélange : MAJuscules + minuscules + chiffres + symboles\nUnique pour chaque compte (jamais le même)\nNe pas utiliser : prénom, date de naissance, 123456\nChangez-le si vous suspectez une compromission"],
                        ['icon' => '🔑', 'title' => 'La double authentification (2FA)', 'text' => "La 2FA ajoute une couche de sécurité : même avec votre mot de passe volé, le pirate ne peut pas se connecter sans votre téléphone.\nMéthodes : SMS (moins sécurisé), Application (Google Authenticator, Authy), Clé physique (YubiKey)."],
                        ['icon' => '🛡️', 'title' => 'Gestionnaire de mots de passe', 'text' => "Bitwarden (gratuit, open source) — Recommandé\n1Password (payant, professionnel)\nDashlane (interface intuitive)\n\nUn gestionnaire génère et stocke des mots de passe complexes. Vous n'avez besoin de mémoriser qu'un seul mot de passe maître."],
                    ],
                    5 => [
                        ['icon' => '🚨', 'title' => 'Je suis tombé dans le piège — Que faire ?', 'text' => "1. Ne paniquez pas\n2. Déconnectez-vous d'Internet immédiatement\n3. Changez vos mots de passe (depuis un autre appareil)\n4. Activez la 2FA sur tous vos comptes\n5. Signalez l'incident via SPG\n6. Contactez votre équipe IT dans les 30 minutes\n7. Surveillez vos comptes bancaires"],
                        ['icon' => '📋', 'title' => 'Procédure de réponse aux incidents', 'text' => "Détection → Signalement (SPG) → Isolation → Analyse → Correction → Rapport\n\nChaque étape est documentée pour améliorer la défense collective de l'organisation."],
                        ['icon' => '📈', 'title' => 'Améliorer votre score SPG', 'text' => "Signalement de menaces : +10 points\nRéussite de formations : +20 à +40 points\nDétection des simulations : +15 points\nObjectif 100% : Certificat de Vigilance SPG"],
                    ],
                    default => [
                        ['icon' => '📚', 'title' => 'Contenu du module', 'text' => $training->description],
                    ]
                };
            @endphp

            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($contentBlocks as $block)
                    <div style="padding:20px;background:var(--bg-input);border-radius:var(--radius);border-left:3px solid var(--emerald);">
                        <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:10px;">
                            {{ $block['icon'] }} {{ $block['title'] }}
                        </div>
                        <div style="font-size:13px;color:var(--text-secondary);line-height:1.9;white-space:pre-line;">{{ $block['text'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quiz -->
        @if(!empty($quizData) && is_array($quizData) && count($quizData) > 0)
            <div class="cyber-card" id="quizSection">
                <div class="cyber-card-header">
                    <div class="cyber-card-title"><i class="bi bi-patch-question-fill"></i> QUIZ D'ÉVALUATION</div>
                    <span class="cyber-badge badge-info">{{ count($quizData) }} question(s)</span>
                </div>

                <div style="padding:14px;background:var(--bg-input);border-radius:var(--radius);margin-bottom:20px;font-size:12px;color:var(--text-secondary);">
                    <i class="bi bi-info-circle"></i> Répondez à toutes les questions. Un score de <strong style="color:var(--emerald);">70%</strong> minimum est requis pour valider la formation.
                </div>

                <div id="quizContainer">
                    @foreach($quizData as $i => $q)
                        <div style="margin-bottom:20px;padding:20px;background:var(--bg-input);border-radius:var(--radius);border:1px solid var(--border-subtle);">
                            <div style="font-size:14px;font-weight:600;margin-bottom:14px;color:var(--text-primary);">
                                <span style="color:var(--emerald);font-family:'JetBrains Mono',monospace;margin-right:8px;">Q{{ $i + 1 }}.</span>
                                {{ $q['question'] }}
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                @foreach($q['options'] as $j => $opt)
                                    <label class="quiz-option" id="opt-{{ $i }}-{{ $j }}">
                                        <input type="radio" name="q{{ $i }}" value="{{ $j }}"
                                               style="accent-color:var(--emerald);cursor:pointer;">
                                        <span style="font-size:13px;cursor:pointer;">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Résultat quiz -->
                <div id="quizResult" style="display:none;padding:24px;border-radius:var(--radius);text-align:center;margin-bottom:16px;"></div>

                <button onclick="submitQuiz()" id="quizBtn"
                        class="btn-cyber btn-cyber-primary w-100 justify-content-center" style="padding:14px;">
                    <i class="bi bi-send-fill"></i> SOUMETTRE MES RÉPONSES
                </button>
            </div>

        @else
            <!-- Pas de quiz — marquer comme complété -->
            <div class="cyber-card" style="text-align:center;padding:40px;">
                <i class="bi bi-check-circle-fill" style="font-size:44px;color:var(--emerald);display:block;margin-bottom:14px;"></i>
                <div style="font-size:15px;font-weight:600;margin-bottom:6px;">Vous avez lu le contenu de cette formation.</div>
                <div style="font-size:13px;color:var(--text-secondary);margin-bottom:20px;">Cliquez ci-dessous pour valider et obtenir vos points.</div>
                <button onclick="markComplete()" class="btn-cyber btn-cyber-success justify-content-center" style="padding:12px 32px;">
                    <i class="bi bi-check-lg"></i> VALIDER LA FORMATION (+{{ $training->points_reward }} pts)
                </button>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
    const quizData    = @json($quizData ?? []);
    const completeUrl = "{{ route('user.training.complete', $training) }}";
    const CSRF        = "{{ csrf_token() }}";

    // Highlight option au clic
    document.querySelectorAll('.quiz-option').forEach(label => {
        label.addEventListener('click', () => {
            const name = label.querySelector('input').name;
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                const opt = r.closest('.quiz-option');
                opt.style.background    = '';
                opt.style.borderColor   = 'var(--border-subtle)';
                opt.style.color         = 'var(--text-primary)';
            });
            label.style.background  = 'var(--emerald-dim)';
            label.style.borderColor = 'var(--border-muted)';
        });
    });

    async function submitQuiz() {
        if (!quizData || quizData.length === 0) { markComplete(); return; }

        let correct = 0, allAnswered = true;

        quizData.forEach((q, i) => {
            const selected = document.querySelector(`input[name="q${i}"]:checked`);
            if (!selected) { allAnswered = false; return; }

            const val         = parseInt(selected.value);
            const correct_ans = parseInt(q.answer);

            document.querySelectorAll(`input[name="q${i}"]`).forEach(r => {
                const opt = r.closest('.quiz-option');
                const idx = parseInt(r.value);
                if (idx === correct_ans) {
                    opt.style.background  = 'rgba(16,185,129,0.12)';
                    opt.style.borderColor = 'var(--emerald)';
                    opt.style.color       = 'var(--emerald)';
                } else if (r.checked && idx !== correct_ans) {
                    opt.style.background  = 'rgba(244,63,94,0.12)';
                    opt.style.borderColor = 'var(--rose)';
                    opt.style.color       = 'var(--rose)';
                }
                r.disabled = true;
            });

            if (val === correct_ans) correct++;
        });

        if (!allAnswered) {
            alert('⚠️ Veuillez répondre à toutes les questions avant de soumettre.');
            return;
        }

        const score = Math.round((correct / quizData.length) * 100);
        document.getElementById('quizBtn').disabled = true;
        document.getElementById('quizBtn').innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';

        try {
            const res  = await fetch(completeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ score })
            });
            const data = await res.json();

            const result  = document.getElementById('quizResult');
            result.style.display  = 'block';
            const passed  = data.passed;
            result.style.background = passed ? 'rgba(16,185,129,0.08)' : 'rgba(244,63,94,0.08)';
            result.style.border     = `1px solid ${passed ? 'var(--border-muted)' : 'rgba(244,63,94,.2)'}`;
            result.style.borderRadius = 'var(--radius)';

            result.innerHTML = `
                <div style="font-size:48px;margin-bottom:10px;">${passed ? '🏆' : '❌'}</div>
                <div style="font-size:28px;font-family:'JetBrains Mono',monospace;font-weight:700;color:${passed ? 'var(--emerald)' : 'var(--rose)'};">
                    ${score}%
                </div>
                <div style="font-size:14px;color:var(--text-secondary);margin:8px 0;">
                    ${correct}/${quizData.length} réponses correctes
                </div>
                <div style="font-size:13px;color:var(--text-primary);margin-top:10px;padding:12px;background:var(--bg-input);border-radius:var(--radius);">
                    ${data.message}
                </div>
                ${passed
                    ? `<div style="margin-top:16px;display:flex;gap:10px;justify-content:center;">
                        <a href="/user/training" class="btn-cyber btn-cyber-primary"><i class="bi bi-grid-fill"></i> Voir mes formations</a>
                        <a href="/user/training/${@json($training->id)}/certificate" class="btn-cyber btn-cyber-success"><i class="bi bi-file-earmark-pdf-fill"></i> Télécharger certificat</a>
                      </div>`
                    : `<button onclick="location.reload()" class="btn-cyber btn-cyber-warning mt-3"><i class="bi bi-arrow-repeat"></i> Réessayer</button>`
                }
            `;

            document.getElementById('quizBtn').style.display = 'none';
        } catch(e) {
            alert('Erreur lors de la soumission. Réessayez.');
            document.getElementById('quizBtn').disabled = false;
            document.getElementById('quizBtn').innerHTML = '<i class="bi bi-send-fill"></i> SOUMETTRE MES RÉPONSES';
        }
    }

    async function markComplete() {
        try {
            const res  = await fetch(completeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ score: 100 })
            });
            const data = await res.json();
            alert(data.message);
            window.location.href = '/user/training';
        } catch(e) {
            alert('Erreur. Réessayez.');
        }
    }
    </script>
@endsection
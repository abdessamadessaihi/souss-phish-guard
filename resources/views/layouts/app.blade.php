<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Souss Phish Guard') — SPG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/cyber-style.css') }}" rel="stylesheet">
<style>
        /* ═══════════════════════════════════════════
           LAYOUT SHELL — sidebar fixe + main décalé
        ═══════════════════════════════════════════ */

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* on gère le scroll dans .page-content */
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 240px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--bg-surface, #0d1117);
            border-right: 1px solid var(--border-default, rgba(255,255,255,.08));
            z-index: 900;
            transition: width .25s ease, transform .25s ease;
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: 64px;
        }

        /* hide text labels when collapsed */
        #sidebar.collapsed .nav-item span,
        #sidebar.collapsed .user-info,
        #sidebar.collapsed .nav-section-title,
        #sidebar.collapsed .logout-btn span,
        #sidebar.collapsed .tl-label,
        #sidebar.collapsed .tl-bar,
        #sidebar.collapsed .tl-text {
            display: none;
        }

        #sidebar.collapsed .sidebar-brand img { height: 28px; }

        /* ── Main wrapper ── */
        #mainWrap {
            margin-left: 240px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .25s ease;
            overflow: hidden;
        }

        #mainWrap.expanded {
            margin-left: 64px;
        }

        /* ── Topbar ── */
        .topbar {
            flex-shrink: 0;
            height: 56px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            background: var(--bg-surface, #0d1117);
            border-bottom: 1px solid var(--border-default, rgba(255,255,255,.08));
            z-index: 800;
        }

        /* ── Scrollable page area ── */
        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            /* keep background transparent so cyber-bg grid shows */
        }

        /* ══ Sidebar internals ══ */
        .sidebar-brand {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 16px 14px;
            border-bottom: 1px solid var(--border-subtle, rgba(255,255,255,.05));
        }

        .sidebar-user {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-subtle, rgba(255,255,255,.05));
        }

        .user-avatar-wrap { position: relative; flex-shrink: 0; }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: var(--emerald, #10b981);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }

        .user-status {
            position: absolute; bottom: -2px; right: -2px;
            width: 9px; height: 9px;
            border-radius: 50%;
            background: var(--emerald, #10b981);
            border: 2px solid var(--bg-surface, #0d1117);
        }

        .user-name {
            font-size: 13px; font-weight: 600;
            color: var(--text-primary, #e2e8f0);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-secondary, #94a3b8);
            white-space: nowrap;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .nav-section-title {
            font-size: 9px;
            letter-spacing: 2px;
            color: var(--text-muted, #64748b);
            padding: 12px 16px 4px;
            text-transform: uppercase;
            font-family: 'JetBrains Mono', monospace;
            white-space: nowrap;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: var(--text-secondary, #94a3b8);
            text-decoration: none;
            font-size: 13px;
            border-left: 3px solid transparent;
            transition: all .15s;
            white-space: nowrap;
        }

        .nav-item i { font-size: 16px; flex-shrink: 0; }

        .nav-item:hover {
            color: var(--text-primary, #e2e8f0);
            background: var(--bg-elevated, rgba(255,255,255,.04));
        }

        .nav-item.active {
            color: var(--emerald, #10b981);
            background: rgba(16,185,129,.08);
            border-left-color: var(--emerald, #10b981);
        }

        /* Footer */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 12px 0 8px;
            border-top: 1px solid var(--border-subtle, rgba(255,255,255,.05));
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 16px;
            background: none;
            border: none;
            color: var(--text-secondary, #94a3b8);
            font-size: 13px;
            cursor: pointer;
            text-align: left;
            transition: color .15s;
            white-space: nowrap;
        }

        .logout-btn i { font-size: 16px; flex-shrink: 0; }

        .logout-btn:hover { color: var(--rose, #f43f5e); }

        .threat-level {
            padding: 10px 16px 4px;
        }

        .tl-label {
            font-size: 9px;
            letter-spacing: 2px;
            color: var(--text-muted, #64748b);
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 5px;
        }

        .tl-bar {
            height: 3px;
            background: rgba(255,255,255,.08);
            border-radius: 2px;
            overflow: hidden;
        }

        .tl-fill {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #ef4444);
            border-radius: 2px;
        }

        .tl-text {
            font-size: 9px;
            color: #ef4444;
            letter-spacing: 2px;
            font-family: 'JetBrains Mono', monospace;
            text-align: right;
            margin-top: 3px;
        }

        /* ══ Topbar internals ══ */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--text-secondary, #94a3b8);
            font-size: 18px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            transition: color .15s;
            flex-shrink: 0;
        }

        .sidebar-toggle:hover { color: var(--text-primary, #e2e8f0); }

        .topbar-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            letter-spacing: 3px;
            color: var(--text-secondary, #94a3b8);
            text-transform: uppercase;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--text-muted, #64748b);
        }

        .topbar-notif {
            position: relative;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-secondary, #94a3b8);
            transition: color .15s;
        }

        .topbar-notif:hover { color: var(--text-primary, #e2e8f0); }

        .notif-dot {
            position: absolute;
            top: -2px; right: -2px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--rose, #f43f5e);
            border: 2px solid var(--bg-surface, #0d1117);
        }

        .topbar-score {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--text-secondary, #94a3b8);
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .topbar-score i { color: var(--emerald, #10b981); }
        .topbar-score strong { color: var(--emerald, #10b981); }

        /* ══ Alerts ══ */
        .cyber-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .cyber-alert button {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            opacity: .6;
        }

        .cyber-alert.success {
            background: rgba(16,185,129,.1);
            border: 1px solid rgba(16,185,129,.3);
            color: #10b981;
        }

        .cyber-alert.danger {
            background: rgba(244,63,94,.1);
            border: 1px solid rgba(244,63,94,.3);
            color: #f43f5e;
        }

        /* ══ Mobile ══ */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                width: 240px;
            }

            #sidebar.mobile-open {
                transform: translateX(0);
            }

            #mainWrap {
                margin-left: 0 !important;
            }

            .page-content { padding: 16px; }
        }
    </style>
</head>

<body>
    {{-- Animated cyber background --}}
    <div class="cyber-bg">
        <div class="cyber-grid"></div>
    </div>

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.svg') }}" alt="SPG">
        </div>

        {{-- User --}}
        <div class="sidebar-user">
            <div class="user-avatar-wrap">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-status"></div>
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">
                    @if(auth()->user()->isAdmin())
                        <i class="bi bi-shield-fill" style="color:var(--amber);"></i> Guardian
                    @else
                        <i class="bi bi-person-fill" style="color:var(--sky);"></i> Analyste
                    @endif
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                <div class="nav-section-title">Command Center</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle-fill"></i><span>Alertes</span>
                </a>
                <a href="{{ route('admin.simulations.index') }}"
                   class="nav-item {{ request()->routeIs('admin.simulations.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-fill"></i><span>Simulations</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i><span>Utilisateurs</span>
                </a>
                <a href="{{ route('admin.forensic.index') }}"
                   class="nav-item {{ request()->routeIs('admin.forensic.*') ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill"></i><span>IA Forensic</span>
                </a>
                <a href="{{ route('admin.messages.index') }}"
                   class="nav-item {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i><span>Messagerie</span>
                </a>
            @else
                <div class="nav-section-title">Espace Agent</div>
                <a href="{{ route('user.dashboard') }}"
                   class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('user.reports.index') }}"
                   class="nav-item {{ request()->routeIs('user.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-flag-fill"></i><span>Signalements</span>
                </a>
                <a href="{{ route('user.analyzer.index') }}"
                   class="nav-item {{ request()->routeIs('user.analyzer.*') ? 'active' : '' }}">
                    <i class="bi bi-radar"></i><span>Analyseur IA</span>
                </a>
                <a href="{{ route('user.training.index') }}"
                   class="nav-item {{ request()->routeIs('user.training.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i><span>Formation</span>
                </a>
                <a href="{{ route('user.messages.index') }}"
                   class="nav-item {{ request()->routeIs('user.messages.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots-fill"></i><span>Messagerie</span>
                </a>
            @endif
        </nav>

        {{-- Footer: logout + threat level --}}
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-power"></i><span>Déconnexion</span>
                </button>
            </form>
            <div class="threat-level">
                <div class="tl-label">Niveau Menace</div>
                <div class="tl-bar"><div class="tl-fill" style="width:65%"></div></div>
                <div class="tl-text">ÉLEVÉ</div>
            </div>
        </div>

    </aside>

    {{-- ══════════════ MAIN AREA ══════════════ --}}
    <div class="main-wrap" id="mainWrap">

        {{-- Topbar --}}
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-right">
                <div class="topbar-time" id="clock"></div>

                {{-- Language switcher --}}
                <div style="position:relative;">
                    <button id="langBtn" onclick="toggleLang()"
                        style="background:var(--bg-elevated,rgba(255,255,255,.06));border:1px solid var(--border-subtle,rgba(255,255,255,.06));
                               color:var(--text-primary,#e2e8f0);padding:5px 11px;border-radius:6px;cursor:pointer;
                               font-size:11px;font-family:'JetBrains Mono',monospace;display:flex;align-items:center;gap:6px;
                               transition:border-color .2s;"
                        onmouseover="this.style.borderColor='var(--emerald,#10b981)'"
                        onmouseout="this.style.borderColor='var(--border-subtle,rgba(255,255,255,.06))'">
                        <i class="bi bi-globe"></i>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <div id="langMenu"
                        style="display:none;position:absolute;top:38px;right:0;
                               background:var(--bg-surface,#0d1117);border:1px solid var(--border-default,rgba(255,255,255,.08));
                               border-radius:8px;overflow:hidden;min-width:130px;
                               box-shadow:0 8px 24px rgba(0,0,0,.4);z-index:1001;">
                        <a href="{{ route('lang.switch', 'fr') }}"
                           style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:12px;
                                  color:var(--text-primary,#e2e8f0);text-decoration:none;transition:background .15s;"
                           onmouseover="this.style.background='var(--bg-elevated)'"
                           onmouseout="this.style.background=''">
                            <span>🇫🇷</span> Français
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}"
                           style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:12px;
                                  color:var(--text-primary,#e2e8f0);text-decoration:none;transition:background .15s;"
                           onmouseover="this.style.background='var(--bg-elevated)'"
                           onmouseout="this.style.background=''">
                            <span>🇬🇧</span> English
                        </a>
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="topbar-notif" id="notifToggle" onclick="toggleNotif()">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notif-dot" id="notifDot" style="display:none;"></span>
                    <span id="notifCount"
                        style="display:none;position:absolute;top:-7px;right:-8px;background:var(--rose,#f43f5e);
                               color:#fff;font-size:8px;min-width:16px;height:16px;border-radius:8px;
                               align-items:center;justify-content:center;padding:0 3px;
                               font-family:'JetBrains Mono',monospace;"></span>
                </div>

                {{-- Score --}}
                <div class="topbar-score">
                    <i class="bi bi-shield-check"></i>
                    Score : <strong>{{ auth()->user()->vigilance_score }}</strong>
                </div>
            </div>
        </div>

        {{-- Notifications panel --}}
        <div id="notifPanel"
            style="display:none;position:fixed;top:62px;right:16px;width:340px;max-height:460px;
                   background:var(--bg-surface,#0d1117);border:1px solid var(--border-default,rgba(255,255,255,.08));
                   border-radius:12px;z-index:1000;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.5);">
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:12px 16px;border-bottom:1px solid var(--border-subtle,rgba(255,255,255,.05));">
                <div style="font-family:'JetBrains Mono',monospace;font-size:10px;
                            color:var(--emerald,#10b981);letter-spacing:2px;">
                    <i class="bi bi-bell-fill"></i> NOTIFICATIONS
                </div>
                <button onclick="markAllRead()"
                    style="background:none;border:none;font-size:11px;color:var(--text-secondary,#94a3b8);cursor:pointer;">
                    Tout marquer lu
                </button>
            </div>
            <div id="notifList" style="overflow-y:auto;max-height:380px;"></div>
        </div>

        {{-- Page content --}}
        <div class="page-content">
            @if(session('success'))
                <div class="cyber-alert success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="cyber-alert danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- ══════════════ CHATBOT ══════════════ --}}
    <div class="chatbot-btn" id="chatbotBtn">
        <i class="bi bi-robot"></i>
        <span class="chatbot-pulse"></span>
    </div>
    <div class="chatbot-panel" id="chatbotPanel">
        <div class="chatbot-header">
            <div class="chatbot-title"><i class="bi bi-shield-fill-check"></i> SPG Assistant</div>
            <button onclick="document.getElementById('chatbotPanel').classList.remove('open')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="chatbot-messages" id="chatMessages">
            <div class="chat-msg bot">
                <i class="bi bi-robot"></i>
                <div class="chat-bubble">Bonjour ! Je suis SPG Assistant. Posez vos questions sur la cybersécurité,
                    ou collez une URL/email à analyser.</div>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatInput" placeholder="Posez votre question..." autocomplete="off">
            <button id="chatSend"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const CSRF = document.querySelector('meta[name=csrf-token]').content;

        // ── CLOCK ──
        function updateClock() {
            const n = new Date();
            document.getElementById('clock').textContent =
                n.toLocaleTimeString('fr-FR') + ' | ' + n.toLocaleDateString('fr-FR');
        }
        setInterval(updateClock, 1000); updateClock();

        // ── SIDEBAR TOGGLE ──
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            const sidebar  = document.getElementById('sidebar');
            const mainWrap = document.getElementById('mainWrap');

            // Mobile vs desktop behaviour
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainWrap.classList.toggle('expanded');
            }
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            const sidebar  = document.getElementById('sidebar');
            const toggle   = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 768 &&
                sidebar.classList.contains('mobile-open') &&
                !sidebar.contains(e.target) &&
                !toggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });

        // ── LANGUAGE SWITCHER ──
        function toggleLang() {
            const m = document.getElementById('langMenu');
            m.style.display = m.style.display === 'none' ? 'block' : 'none';
        }
        document.addEventListener('click', e => {
            const btn  = document.getElementById('langBtn');
            const menu = document.getElementById('langMenu');
            if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // ── CHATBOT ──
        let chatHistory = [];

        document.getElementById('chatbotBtn').addEventListener('click', () => {
            document.getElementById('chatbotPanel').classList.toggle('open');
        });
        document.getElementById('chatSend').addEventListener('click', sendChat);
        document.getElementById('chatInput').addEventListener('keypress', e => {
            if (e.key === 'Enter') sendChat();
        });

        async function sendChat() {
            const input = document.getElementById('chatInput');
            const msg   = input.value.trim();
            if (!msg) return;
            input.value = '';

            const msgs = document.getElementById('chatMessages');
            msgs.innerHTML += `<div class="chat-msg user"><div class="chat-bubble">${escapeHtml(msg)}</div></div>`;

            const typingId = 'typing_' + Date.now();
            msgs.innerHTML += `<div class="chat-msg bot" id="${typingId}"><i class="bi bi-robot"></i>
                <div class="chat-bubble"><span class="typing-dots"><span></span><span></span><span></span></span></div></div>`;
            msgs.scrollTop = msgs.scrollHeight;

            chatHistory.push({ role: 'user', content: msg });
            if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);

            try {
                const res  = await fetch('{{ route("user.analyzer.scan") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ content: msg, type: 'chat', history: chatHistory.slice(0, -1) })
                });
                const data = await res.json();
                document.getElementById(typingId)?.remove();

                const reply = data.result || 'Je n\'ai pas pu répondre. Réessayez.';
                msgs.innerHTML += `<div class="chat-msg bot"><i class="bi bi-robot"></i>
                    <div class="chat-bubble">${reply}</div></div>`;

                chatHistory.push({ role: 'assistant', content: reply.replace(/<[^>]*>/g, '') });
            } catch (e) {
                document.getElementById(typingId)?.remove();
                msgs.innerHTML += `<div class="chat-msg bot"><i class="bi bi-robot"></i>
                    <div class="chat-bubble" style="color:var(--rose,#f43f5e);">Service indisponible.</div></div>`;
            }
            msgs.scrollTop = msgs.scrollHeight;
        }

        function escapeHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        }

        // ── NOTIFICATIONS ──
        let notifOpen = false;

        function toggleNotif() {
            notifOpen = !notifOpen;
            document.getElementById('notifPanel').style.display = notifOpen ? 'block' : 'none';
            if (notifOpen) loadNotifications();
        }

        document.addEventListener('click', (e) => {
            const panel  = document.getElementById('notifPanel');
            const toggle = document.getElementById('notifToggle');
            if (notifOpen && panel && !panel.contains(e.target) && !toggle.contains(e.target)) {
                notifOpen = false;
                panel.style.display = 'none';
            }
        });

        async function loadNotifications() {
            try {
                const res  = await fetch('{{ route("notifications.index") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                const data = await res.json();
                const count = data.unread || 0;

                const countEl = document.getElementById('notifCount');
                const dotEl   = document.getElementById('notifDot');

                if (count > 0) {
                    countEl.textContent = count > 99 ? '99+' : count;
                    countEl.style.display = 'flex';
                    dotEl.style.display = 'block';
                } else {
                    countEl.style.display = 'none';
                    dotEl.style.display = 'none';
                }

                renderNotifications(data.notifications || []);
            } catch (e) {}
        }

        function renderNotifications(notifs) {
            const list = document.getElementById('notifList');
            if (!notifs.length) {
                list.innerHTML = `<div style="text-align:center;padding:40px;color:var(--text-muted,#64748b);font-size:13px;">
                    <i class="bi bi-bell-slash" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                    Aucune notification</div>`;
                return;
            }
            const colorMap = {
                red:'var(--rose,#f43f5e)', amber:'var(--amber,#f59e0b)',
                green:'var(--emerald,#10b981)', cyan:'var(--sky,#38bdf8)',
                orange:'var(--amber,#f59e0b)', purple:'var(--violet,#8b5cf6)'
            };
            list.innerHTML = notifs.map(n => `
                <div onclick="readNotif(${n.id},'${n.link}')"
                     style="display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border-subtle,rgba(255,255,255,.05));
                            cursor:pointer;background:${!n.is_read ? 'rgba(16,185,129,.03)' : 'transparent'};transition:background .15s;"
                     onmouseover="this.style.background='var(--bg-elevated)'"
                     onmouseout="this.style.background='${!n.is_read ? 'rgba(16,185,129,.03)' : 'transparent'}'">
                    <div style="width:34px;height:34px;border-radius:6px;display:flex;align-items:center;justify-content:center;
                                font-size:15px;flex-shrink:0;background:var(--bg-elevated);
                                color:${colorMap[n.color] || 'var(--emerald,#10b981)'};">
                        <i class="bi ${n.icon}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:${!n.is_read ? '600' : '400'};
                                    color:var(--text-primary,#e2e8f0);margin-bottom:2px;display:flex;align-items:center;gap:6px;">
                            ${n.title}
                            ${!n.is_read ? '<span style="width:5px;height:5px;border-radius:50%;background:var(--emerald,#10b981);flex-shrink:0;"></span>' : ''}
                        </div>
                        <div style="font-size:11px;color:var(--text-secondary,#94a3b8);
                                    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${n.body}</div>
                        <div style="font-size:10px;color:var(--text-muted,#64748b);margin-top:3px;
                                    font-family:'JetBrains Mono',monospace;">${timeAgo(n.created_at)}</div>
                    </div>
                </div>`).join('');
        }

        async function readNotif(id, link) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
            } catch (e) {}
            if (link && link !== '#') window.location.href = link;
        }

        async function markAllRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                loadNotifications();
            } catch (e) {}
        }

        function timeAgo(d) {
            if (!d) return '';
            const diff = Math.floor((Date.now() - new Date(d).getTime()) / 1000);
            if (diff < 60)    return 'À l\'instant';
            if (diff < 3600)  return Math.floor(diff / 60) + ' min';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h';
            return Math.floor(diff / 86400) + 'j';
        }

        loadNotifications();
        setInterval(loadNotifications, 30000);
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
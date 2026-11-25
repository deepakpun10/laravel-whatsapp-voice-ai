<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Pundeer AI SalesHub</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <style>
        :root {
            --navy: #101b35;
            --navy2: #182747;
            --blue: #3978f6;
            --cyan: #20b8c9;
            --purple: #8057e8;
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #182238;
            --muted: #778198;
            --line: #e8ecf3;
            --green: #16a779;
            --orange: #e99a35;
            --red: #e45c68;
            --shadow: 0 8px 28px rgba(27,43,76,.07);
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); font-family: Inter, system-ui, -apple-system, sans-serif; color: var(--text); }
        .app-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--navy); color: #dce5f7; padding: 20px 14px; position: fixed; inset: 0 auto 0 0; overflow-y: auto; z-index: 40; transition: transform .25s ease; }
        .brand { display: flex; align-items: center; gap: 10px; padding: 4px 9px 22px; font-weight: 800; font-size: 17px; color: white; border-bottom: 1px solid #1e2e4f; margin-bottom: 12px; }
        .logo { width: 36px; height: 36px; border-radius: 11px; background: linear-gradient(135deg, #4387ff, #8a5ce8); display: grid; place-items: center; font-weight: 900; color: white; font-size: 18px; }
        .brand small { display: block; font-size: 10px; color: #93a4c4; font-weight: 500; letter-spacing: 1.5px; margin-top: 2px; }
        .navlabel { font-size: 10px; color: #8494b4; text-transform: uppercase; letter-spacing: 1.5px; padding: 14px 11px 6px; font-weight: 700; }
        .nav-item { width: 100%; border: 0; background: transparent; color: #b9c6df; text-align: left; padding: 9px 12px; border-radius: 9px; display: flex; align-items: center; gap: 11px; margin: 2px 0; font-size: 13.5px; font-weight: 500; text-decoration: none; transition: all .15s; }
        .nav-item:hover, .nav-item.active { background: #26395f; color: #fff; font-weight: 600; }
        .nav-item .ico { width: 20px; text-align: center; font-size: 16px; opacity: .85; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); min-width: 0; display: flex; flex-direction: column; }
        .topbar { height: 68px; background: #fff; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 30; }
        .crumb { color: var(--muted); font-size: 13.5px; font-weight: 500; }
        .pill { background: #edf3ff; color: #3568ca; border-radius: 30px; padding: 6px 12px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #e9e1ff; color: #6544bd; display: grid; place-items: center; font-weight: 700; font-size: 13px; }
        .content-area { padding: 26px; max-width: 1600px; margin: 0 auto; width: 100%; }
        .mobile-overlay { display: none; }

        @media (max-width: 950px) {
            .sidebar { transform: translateX(-100%); width: 265px; }
            .sidebar.open { transform: translateX(0); }
            .mobile-overlay.show { display: block; position: fixed; inset: 0; background: rgba(7, 19, 44, 0.55); z-index: 35; }
            .main-content { margin-left: 0; width: 100%; }
            .hamb { display: block !important; }
            .topbar { padding: 0 14px; height: 60px; gap: 8px; }
            .content-area { padding: 14px 12px; }
        }
        @media (max-width: 768px) {
            html, body { overflow-x: hidden; max-width: 100%; }
            .topbar .crumb { display: none; }
            .topbar .pill { display: none; }
            .content-area table { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .content-area [style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
            .content-area [style*="display:flex"][style*="justify-content:space-between"] {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand" style="text-decoration:none;">
            <x-app-logo-icon style="width:36px;height:36px;border-radius:10px;flex-shrink:0;" />
            <div>PUNDEER <small>AI SALESHUB</small></div>
        </a>

        <nav class="nav">
            <div class="navlabel">Workspace</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="ico">▦</span>Dashboard
            </a>
            <a href="{{ route('admin.conversations.index') }}" class="nav-item {{ request()->routeIs('admin.conversations.*') ? 'active' : '' }}">
                <span class="ico">◉</span>WhatsApp Inbox
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <span class="ico">♙</span>Contacts & Leads
            </a>
            <a href="{{ route('admin.calls.index') }}" class="nav-item {{ request()->routeIs('admin.calls.*') ? 'active' : '' }}">
                <span class="ico">☎</span>Voice Calls
            </a>

            <div class="navlabel">AI Studio</div>
            <a href="{{ route('admin.ai-agents.index') }}" class="nav-item {{ request()->routeIs('admin.ai-agents.*') ? 'active' : '' }}">
                <span class="ico">✧</span>AI Agents
            </a>
            <a href="{{ route('admin.phone-numbers.index') }}" class="nav-item {{ request()->routeIs('admin.phone-numbers.*') ? 'active' : '' }}">
                <span class="ico">📞</span>Phone Numbers
            </a>
            <a href="{{ route('admin.calling-settings.index') }}" class="nav-item {{ request()->routeIs('admin.calling-settings.*') ? 'active' : '' }}">
                <span class="ico">⚙️</span>Calling Settings
            </a>
            <a href="{{ route('admin.interactive.index') }}" class="nav-item {{ request()->routeIs('admin.interactive.*') ? 'active' : '' }}">
                <span class="ico">🗂️</span>Interactive Messages
            </a>
            <a href="{{ route('admin.knowledge-bases.index') }}" class="nav-item {{ request()->routeIs('admin.knowledge-bases.*') ? 'active' : '' }}">
                <span class="ico">▤</span>Knowledge Bases
            </a>
            <a href="{{ route('admin.routing.index') }}" class="nav-item {{ request()->routeIs('admin.routing.*') ? 'active' : '' }}">
                <span class="ico">⇄</span>Business Routing
            </a>

            <div class="navlabel">Automation</div>
            <a href="{{ route('admin.calling-workflows.index') }}" class="nav-item {{ request()->routeIs('admin.calling-workflows.*') ? 'active' : '' }}">
                <span class="ico">⚡</span>Calling Workflows
            </a>
            <a href="{{ route('admin.workflows.index') }}" class="nav-item {{ request()->routeIs('admin.workflows.*') ? 'active' : '' }}">
                <span class="ico">⌘</span>Workflow Builder
            </a>
            <a href="{{ route('admin.campaigns.index') }}" class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                <span class="ico">➤</span>Campaigns
            </a>
            <a href="{{ route('admin.follow-ups.index') }}" class="nav-item {{ request()->routeIs('admin.follow-ups.*') ? 'active' : '' }}">
                <span class="ico">◷</span>Follow-ups
            </a>

            <div class="navlabel">Insights & Setup</div>
            <a href="{{ route('admin.analytics.index') }}" class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <span class="ico">▥</span>Analytics
            </a>
            <a href="{{ route('admin.integrations.index') }}" class="nav-item {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                <span class="ico">⛓</span>Integrations & API
            </a>
            <a href="{{ route('admin.businesses.index') }}" class="nav-item {{ request()->routeIs('admin.businesses.*') ? 'active' : '' }}">
                <span class="ico">🏢</span>Businesses (Tenants)
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="ico">⚙</span>Business Settings
            </a>
        </nav>

        <div style="border-top:1px solid #233555; margin-top:24px; padding:16px 8px; color:#8ea2c4; font-size:11.5px; line-height:1.6;">
            <div style="display:flex; align-items:center; gap:6px; color:#60a5fa; font-weight:600; margin-bottom:4px;">
                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e;"></span> Production MySQL
            </div>
            Multi-Tenant RAG & Intent Routing
        </div>
    </aside>

    <div class="mobile-overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamb" onclick="toggleSidebar()" style="display:none; border:1px solid #e2e8f0; background:#f8fafc; padding:6px 10px; border-radius:8px; cursor:pointer;">
                    ☰
                </button>
                <span class="crumb">{{ $breadcrumb ?? 'Workspace / Dashboard' }}</span>
            </div>

            <div style="display:flex; align-items:center; gap:14px;">
                <span class="pill">
                    <span style="color:#22c55e;">●</span> SaaS Live
                </span>

                <!-- Workspace Switcher Component -->
                <livewire:admin.workspace-switcher />

                <!-- User Dropdown -->
                <div style="position:relative;" x-data="{ open: false }">
                    <button @click="open = !open" style="display:flex; align-items:center; gap:8px; border:0; background:transparent; cursor:pointer;">
                        <div class="avatar">{{ auth()->user()?->initials() ?? 'AD' }}</div>
                        <span style="font-size:13px; font-weight:600; color:#334155;" class="hidden md:inline">{{ auth()->user()?->name ?? 'Admin' }}</span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak style="position:absolute; right:0; top:45px; background:white; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,.08); width:180px; padding:6px; z-index:50;">
                        <div style="padding:8px 10px; border-bottom:1px solid #f1f5f9; font-size:12px;">
                            <div style="font-weight:700; color:#0f172a;">{{ auth()->user()?->name ?? 'Admin' }}</div>
                            <div style="color:#64748b; font-size:11px;">{{ auth()->user()?->email ?? 'admin@pundeer.ai' }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" style="display:block; padding:8px 10px; font-size:13px; color:#334155; text-decoration:none; border-radius:6px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            Profile & Security
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="width:100%; text-align:left; border:0; background:transparent; padding:8px 10px; font-size:13px; color:#ef4444; border-radius:6px; cursor:pointer;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div style="padding: 12px 28px 0;">
            @if(session('success'))
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; border-radius:8px; font-size:13px; display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <span>✓ {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="border:0; background:transparent; cursor:pointer; color:#065f46; font-weight:bold;">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; font-size:13px; display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <span>⚠ {{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="border:0; background:transparent; cursor:pointer; color:#991b1b; font-weight:bold;">✕</button>
                </div>
            @endif
        </div>

        <!-- Page View Body -->
        <div class="content-area">
            {{ $slot }}
        </div>
    </main>
</div>

<!-- Toast notification box -->
<div id="toast" style="position:fixed; bottom:24px; left:50%; transform:translate(-50%, 20px); opacity:0; pointer-events:none; background:#101b35; color:white; padding:12px 20px; border-radius:10px; z-index:100; box-shadow:0 12px 35px rgba(0,0,0,.25); transition:all .25s ease; font-size:13.5px; display:flex; align-items:center; gap:8px;">
    <span id="toastMsg"></span>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('overlay').classList.toggle('show');
    }

    let toastTimer;
    function showToast(msg) {
        const t = document.getElementById('toast');
        const m = document.getElementById('toastMsg');
        if (!t || !m) return;
        m.textContent = msg;
        t.style.opacity = '1';
        t.style.transform = 'translate(-50%, 0)';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translate(-50%, 20px)';
        }, 3200);
    }

    window.addEventListener('toast', event => {
        showToast(event.detail.message || event.detail[0]?.message || 'Operation successful');
    });
</script>

<livewire:admin.copilot.agentic-copilot />

@livewireScripts
</body>
</html>

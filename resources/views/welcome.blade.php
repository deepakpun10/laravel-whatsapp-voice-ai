<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pundeer AI SalesHub — Multi-Business WhatsApp &amp; Voice AI Platform</title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="alternate icon" href="/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #FAFAFA;
            color: #09090B;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
        .subtle-grid {
            background-image: linear-gradient(to right, rgba(9, 9, 11, 0.04) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(9, 9, 11, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        details > summary {
            list-style: none;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
    </style>
</head>
<body class="bg-[#FAFAFA] text-zinc-950 antialiased selection:bg-zinc-900 selection:text-white">

    <!-- Top Navigation -->
    <header class="sticky top-0 z-50 bg-white/85 backdrop-blur-md border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <x-app-logo-icon class="w-9 h-9 rounded-xl shadow-sm group-hover:scale-105 transition-transform" />
                <div class="flex items-center gap-2">
                    <span class="font-display font-bold text-base tracking-tight text-zinc-950">Pundeer AI</span>
                    <span class="text-[10px] font-mono-code font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                        SalesHub
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-zinc-600">
                <a href="#architecture" class="hover:text-zinc-950 transition-colors">Architecture</a>
                <a href="#routing" class="hover:text-zinc-950 transition-colors">Smart Routing</a>
                <a href="#voice" class="hover:text-zinc-950 transition-colors">Voice Telephony</a>
                <a href="#isolation" class="hover:text-zinc-950 transition-colors">RAG Isolation</a>
                <a href="#faq" class="hover:text-zinc-950 transition-colors">FAQ</a>
            </nav>

            <!-- Auth Actions -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm transition-all">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Open Workspace
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-700 hover:text-zinc-950 px-3 py-2 transition-colors">
                        Sign in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm transition-all">
                            Get Started
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section (Asymmetric Split Layout) -->
    <section class="relative overflow-hidden border-b border-zinc-200/80 bg-white subtle-grid">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20 lg:pt-20 lg:pb-24">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-10 items-center">

                <!-- Left Column: Copy & Actions -->
                <div class="lg:col-span-6 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-medium text-zinc-700 mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>WhatsApp Cloud API + Sarvam AI Voice</span>
                    </div>

                    <h1 class="font-display text-4xl sm:text-5xl lg:text-[54px] font-extrabold tracking-tight text-zinc-950 leading-[1.08]">
                        One WhatsApp Line.<br>
                        <span class="text-emerald-600">Every Business</span> You Run.
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-zinc-600 leading-relaxed max-w-xl">
                        Operate independent brands like <strong class="text-zinc-900 font-semibold">Pundeer Technologies</strong> and <strong class="text-zinc-900 font-semibold">Himachal Garlic</strong> from a single official WhatsApp number and AI voice desk — with automatic intent routing, zero knowledge leakage, and instant human takeover.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="mt-8 flex flex-wrap items-center gap-3.5">
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-sm shadow-sm transition-all">
                                <span>Go to Dashboard</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-sm shadow-sm transition-all">
                                <span>Launch Workspace</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @endauth
                        <a href="#architecture" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-300 font-semibold text-sm transition-colors">
                            <span>How Routing Works</span>
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    <!-- Key Metrics Strip -->
                    <div class="mt-10 pt-8 border-t border-zinc-200 grid grid-cols-3 gap-6 max-w-lg">
                        <div>
                            <div class="font-display text-2xl font-extrabold text-zinc-950">&lt;180ms</div>
                            <div class="text-xs text-zinc-500 mt-0.5">Intent Routing</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl font-extrabold text-emerald-600">0%</div>
                            <div class="text-xs text-zinc-500 mt-0.5">Data Cross-Leak</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl font-extrabold text-zinc-950">24/7</div>
                            <div class="text-xs text-zinc-500 mt-0.5">Hindi &amp; EN Voice</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Realistic Interactive Product UI Card -->
                <div class="lg:col-span-6">
                    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xl shadow-zinc-200/60 overflow-hidden">
                        <!-- Window Chrome -->
                        <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-zinc-300"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-zinc-300"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-zinc-300"></span>
                                <span class="ml-2 text-xs font-mono-code text-zinc-500">live-router · whatsapp-cloud-api</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Connected
                            </span>
                        </div>

                        <!-- Live Simulation Body -->
                        <div class="p-5 space-y-4">
                            <!-- Incoming Customer Message -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-xs font-bold text-zinc-700 shrink-0">
                                    AK
                                </div>
                                <div class="bg-zinc-100 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-md border border-zinc-200/70">
                                    <div class="flex items-center justify-between gap-4 mb-1">
                                        <span class="text-[11px] font-semibold text-zinc-900">Wholesale Buyer · Verified</span>
                                        <span class="text-[10px] text-zinc-400">11:02 AM</span>
                                    </div>
                                    <p class="text-sm text-zinc-800">
                                        "Bhaiya aaj Himachal Grade-A garlic 50kg wholesale ka kya rate chal raha hai?"
                                    </p>
                                </div>
                            </div>

                            <!-- Routing Engine Decision Pill -->
                            <div class="ml-11 bg-zinc-950 text-zinc-100 rounded-xl p-3 text-xs font-mono-code space-y-1.5 shadow-sm">
                                <div class="flex items-center justify-between text-zinc-400">
                                    <span>ROUTING_ENGINE</span>
                                    <span class="text-emerald-400">98.4% confidence · 142ms</span>
                                </div>
                                <div class="flex items-center gap-2 text-white">
                                    <span class="text-emerald-400">→</span>
                                    <span>Assigned: <strong>Himachal Garlic</strong></span>
                                    <span class="text-zinc-500">|</span>
                                    <span class="text-zinc-300">RAG: Garlic_Rates_2026.pdf</span>
                                </div>
                            </div>

                            <!-- AI Verified Reply -->
                            <div class="flex items-start justify-end gap-3">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-md">
                                    <div class="flex items-center justify-between gap-4 mb-1">
                                        <span class="text-[11px] font-semibold text-emerald-800">Himachal Garlic AI</span>
                                        <span class="text-[10px] text-emerald-600">✓✓ Sent</span>
                                    </div>
                                    <p class="text-sm text-zinc-800 leading-relaxed">
                                        Namaste! Aaj Grade-A Bold Himachal Garlic (50kg) ka wholesale rate ₹148/kg hai. Kya main aapke liye dispatch slot ya callback schedule kar doon?
                                    </p>
                                </div>
                            </div>

                            <!-- Voice Telephony Call Card -->
                            <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-zinc-900">Sarvam AI Voice Follow-Up</div>
                                        <div class="text-[11px] text-zinc-500">Autonomous Callback · Duration: 01:14</div>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md">
                                    Meeting Scheduled
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════
         3D ANIMATED DEMO SECTION — WhatsApp → AI → Voice Call
    ══════════════════════════════════════════════════════════ -->
    <section id="demo" class="relative py-28 overflow-hidden" style="background: linear-gradient(135deg, #0a0a14 0%, #0d1117 40%, #0a1628 70%, #071020 100%);">

        <!-- Ambient glows -->
        <div class="absolute inset-0 pointer-events-none">
            <div style="position:absolute;top:10%;left:15%;width:420px;height:420px;background:radial-gradient(circle,rgba(37,211,102,0.12) 0%,transparent 70%);border-radius:50%;filter:blur(40px);"></div>
            <div style="position:absolute;bottom:15%;right:12%;width:360px;height:360px;background:radial-gradient(circle,rgba(99,102,241,0.14) 0%,transparent 70%);border-radius:50%;filter:blur(40px);"></div>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(37,211,102,0.04) 0%,transparent 65%);border-radius:50%;"></div>
        </div>

        <!-- Subtle dot grid overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-20" style="background-image:radial-gradient(circle,rgba(255,255,255,0.15) 1px,transparent 1px);background-size:28px 28px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono-code font-semibold uppercase tracking-wider border mb-4" style="background:rgba(37,211,102,0.08);border-color:rgba(37,211,102,0.25);color:#25d366;">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#25d366;"></span>
                    Live Flow Demo · Scroll Triggered
                </span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
                    Message → AI Reply → <span style="background:linear-gradient(90deg,#25d366,#128c7e);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Voice Call</span>
                </h2>
                <p class="mt-3 text-zinc-400 text-base max-w-xl mx-auto leading-relaxed">
                    Customer sends a WhatsApp message. AI responds instantly. One tap — Pundeer Technologies calls them back automatically.
                </p>

                <!-- Replay & Status Pill -->
                <div class="flex items-center justify-center gap-3 mt-5">
                    <button id="btn-replay-demo" onclick="window.runDemoFlow()" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 hover:border-emerald-500/50 transition-all cursor-pointer shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Replay Live Flow</span>
                    </button>
                    <span id="demo-status-badge" class="text-[11px] font-mono-code px-3 py-1 rounded-full bg-zinc-800/80 text-zinc-400 border border-zinc-700/60">
                        Waiting for scroll...
                    </span>
                </div>
            </div>

            <!-- Main Demo Layout: Steps + Phone(s) -->
            <div class="flex flex-col lg:flex-row items-center justify-center gap-10 lg:gap-16">

                <!-- Left: Step Labels -->
                <div class="hidden lg:flex flex-col gap-8 w-56 text-right" id="demo-steps-left">
                    <div id="demo-step-1" class="demo-step-card transition-all duration-500 opacity-40 p-2.5 rounded-xl border border-transparent">
                        <div class="flex items-center justify-end gap-3">
                            <div>
                                <div class="text-xs font-mono-code font-semibold uppercase tracking-wider mb-0.5 text-emerald-400">Step 01</div>
                                <div class="font-display font-bold text-white text-sm">Customer Messages</div>
                                <div class="text-zinc-400 text-xs mt-0.5 leading-relaxed">Sends WhatsApp query about rates</div>
                            </div>
                            <div class="w-8 h-8 rounded-full border flex items-center justify-center shrink-0 text-sm font-bold bg-emerald-500/10 border-emerald-500/30 text-emerald-400">1</div>
                        </div>
                    </div>
                    <div id="demo-step-2" class="demo-step-card transition-all duration-500 opacity-40 p-2.5 rounded-xl border border-transparent">
                        <div class="flex items-center justify-end gap-3">
                            <div>
                                <div class="text-xs font-mono-code font-semibold uppercase tracking-wider mb-0.5 text-purple-400">Step 02</div>
                                <div class="font-display font-bold text-white text-sm">AI Auto-Replies</div>
                                <div class="text-zinc-400 text-xs mt-0.5 leading-relaxed">RAG-grounded response in &lt;180ms</div>
                            </div>
                            <div class="w-8 h-8 rounded-full border flex items-center justify-center shrink-0 text-sm font-bold bg-purple-500/10 border-purple-500/30 text-purple-400">2</div>
                        </div>
                    </div>
                    <div id="demo-step-3" class="demo-step-card transition-all duration-500 opacity-40 p-2.5 rounded-xl border border-transparent">
                        <div class="flex items-center justify-end gap-3">
                            <div>
                                <div class="text-xs font-mono-code font-semibold uppercase tracking-wider mb-0.5 text-blue-400">Step 03</div>
                                <div class="font-display font-bold text-white text-sm">Customer Taps Call</div>
                                <div class="text-zinc-400 text-xs mt-0.5 leading-relaxed">Taps the WhatsApp CTA button</div>
                            </div>
                            <div class="w-8 h-8 rounded-full border flex items-center justify-center shrink-0 text-sm font-bold bg-blue-500/10 border-blue-500/30 text-blue-400">3</div>
                        </div>
                    </div>
                    <div id="demo-step-4" class="demo-step-card transition-all duration-500 opacity-40 p-2.5 rounded-xl border border-transparent">
                        <div class="flex items-center justify-end gap-3">
                            <div>
                                <div class="text-xs font-mono-code font-semibold uppercase tracking-wider mb-0.5 text-emerald-400">Step 04</div>
                                <div class="font-display font-bold text-white text-sm">AI Calls Back</div>
                                <div class="text-zinc-400 text-xs mt-0.5 leading-relaxed">Sarvam AI dials them instantly</div>
                            </div>
                            <div class="w-8 h-8 rounded-full border flex items-center justify-center shrink-0 text-sm font-bold bg-emerald-500/10 border-emerald-500/30 text-emerald-400">4</div>
                        </div>
                    </div>
                </div>

                <!-- Centre: 3D Phone Mockups Container -->
                <div class="relative flex items-center justify-center" style="perspective:1200px; min-height:580px;">

                    <!-- ── PHONE 1: WhatsApp Chat Screen ── -->
                    <div id="phone-wa" class="relative" style="
                        width: 230px;
                        transform: rotateY(-16deg) rotateX(6deg) rotateZ(-1deg) translateZ(0px);
                        transition: transform 0.9s cubic-bezier(0.34,1.4,0.64,1), filter 0.6s ease;
                        filter: drop-shadow(0 30px 60px rgba(37,211,102,0.22)) drop-shadow(0 10px 30px rgba(0,0,0,0.6));
                    ">
                        <!-- Phone Frame -->
                        <div style="background:linear-gradient(145deg,#1c1c1e,#2c2c2e);border-radius:36px;padding:10px;border:1px solid rgba(255,255,255,0.08);box-shadow:inset 0 1px 0 rgba(255,255,255,0.1),inset 0 -1px 0 rgba(0,0,0,0.3);">
                            <!-- Notch -->
                            <div style="position:relative;background:#000;border-radius:28px;overflow:hidden;min-height:490px;">
                                <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:80px;height:22px;background:#1c1c1e;border-radius:0 0 16px 16px;z-index:10;"></div>

                                <!-- WhatsApp Header -->
                                <div style="background:#075e54;padding:28px 10px 8px;display:flex;align-items:center;gap:8px;">
                                    <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#25d366,#128c7e);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:white;flex-shrink:0;">HG</div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="color:white;font-size:12px;font-weight:700;line-height:1.2;">Himachal Garlic AI</div>
                                        <div style="color:rgba(255,255,255,0.65);font-size:9px;">online</div>
                                    </div>
                                    <div style="color:rgba(255,255,255,0.7);font-size:16px;">⋮</div>
                                </div>

                                <!-- Chat Body -->
                                <div style="background:#ece5dd;padding:10px 8px;min-height:400px;display:flex;flex-direction:column;gap:6px;position:relative;">
                                    <!-- Wallpaper subtle pattern -->
                                    <div style="position:absolute;inset:0;opacity:0.04;background-image:url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"40\" height=\"40\"><path d=\"M0 20 Q10 0 20 20 Q30 40 40 20\" fill=\"none\" stroke=\"%23075e54\" stroke-width=\"1\"/></svg>');background-size:40px 40px;"></div>

                                    <!-- Date pill -->
                                    <div style="text-align:center;margin:4px 0;">
                                        <span style="background:rgba(255,255,255,0.75);color:#667781;font-size:9px;padding:2px 8px;border-radius:6px;font-weight:500;">TODAY</span>
                                    </div>

                                    <!-- Customer message bubble -->
                                    <div id="bubble-customer" style="
                                        display:flex;justify-content:flex-start;
                                        opacity:0;
                                        transform: translateX(-20px) scale(0.9);
                                        transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.34,1.4,0.64,1);
                                    ">
                                        <div style="background:white;border-radius:0 10px 10px 10px;padding:7px 10px;max-width:155px;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                            <p style="color:#111b21;font-size:10px;line-height:1.4;margin:0;">Himachal Grade-A garlic 50kg ka kya rate hai? 🧄</p>
                                            <div style="text-align:right;margin-top:3px;">
                                                <span style="color:#667781;font-size:8px;">11:02 AM</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Typing indicator -->
                                    <div id="typing-indicator" style="
                                        display:flex;justify-content:flex-end;
                                        opacity:0;
                                        transition: opacity 0.3s ease;
                                    ">
                                        <div style="background:#dcf8c6;border-radius:10px 0 10px 10px;padding:9px 14px;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                            <div style="display:flex;gap:3px;align-items:center;height:12px;">
                                                <span style="width:5px;height:5px;border-radius:50%;background:#667781;animation:typingDot 1.2s infinite;animation-delay:0s;"></span>
                                                <span style="width:5px;height:5px;border-radius:50%;background:#667781;animation:typingDot 1.2s infinite;animation-delay:0.2s;"></span>
                                                <span style="width:5px;height:5px;border-radius:50%;background:#667781;animation:typingDot 1.2s infinite;animation-delay:0.4s;"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AI Reply bubble -->
                                    <div id="bubble-ai" style="
                                        display:flex;justify-content:flex-end;
                                        opacity:0;
                                        transform: translateX(20px) scale(0.9);
                                        transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.34,1.4,0.64,1);
                                    ">
                                        <div style="background:#dcf8c6;border-radius:10px 0 10px 10px;padding:7px 10px;max-width:168px;box-shadow:0 1px 2px rgba(0,0,0,0.1);">
                                            <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                                                <span style="font-size:8px;font-weight:700;color:#075e54;">Himachal Garlic AI</span>
                                                <span style="font-size:8px;background:#25d366;color:white;padding:1px 4px;border-radius:3px;font-weight:600;">AI</span>
                                            </div>
                                            <p style="color:#111b21;font-size:10px;line-height:1.5;margin:0;">Namaste! 🙏 Grade-A Bold (50kg) wholesale rate aaj <strong>₹148/kg</strong> hai. Kya aap call chahenge? 📞</p>
                                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:3px;margin-top:3px;">
                                                <span style="color:#667781;font-size:8px;">11:02 AM</span>
                                                <span style="color:#34b7f1;font-size:10px;">✓✓</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CTA Call Button -->
                                    <div id="cta-call-btn" style="
                                        display:flex;justify-content:flex-end;
                                        opacity:0;
                                        transform: translateY(10px) scale(0.9);
                                        transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.34,1.4,0.64,1);
                                    ">
                                        <div id="call-cta-wrapper" style="cursor:pointer;" onclick="triggerCallScreen()">
                                            <div id="call-cta-inner" style="background:#25d366;border-radius:8px;padding:8px 16px;display:flex;align-items:center;gap:6px;box-shadow:0 2px 10px rgba(37,211,102,0.45);transition:transform 0.15s, box-shadow 0.15s;">
                                                <span style="font-size:14px;">📞</span>
                                                <span style="color:white;font-size:10px;font-weight:700;letter-spacing:0.3px;">Call Me Now</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tap ripple hint -->
                                    <div id="tap-hint" style="
                                        display:flex;justify-content:flex-end;
                                        opacity:0;
                                        transition: opacity 0.4s ease;
                                        margin-top:-2px;
                                        padding-right:8px;
                                    ">
                                        <span class="inline-flex items-center gap-1" style="color:#667781;font-size:8px;font-style:italic;">
                                            <span style="animation: tapPulse 1s infinite; display:inline-block;">👆</span>
                                            <span>auto-dialing on tap</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Phone side button -->
                        <div style="position:absolute;right:-3px;top:100px;width:3px;height:40px;background:rgba(255,255,255,0.1);border-radius:0 2px 2px 0;"></div>
                        <div style="position:absolute;left:-3px;top:80px;width:3px;height:28px;background:rgba(255,255,255,0.1);border-radius:2px 0 0 2px;"></div>
                        <div style="position:absolute;left:-3px;top:118px;width:3px;height:28px;background:rgba(255,255,255,0.1);border-radius:2px 0 0 2px;"></div>
                    </div>

                    <!-- ── Connector arrow between phones ── -->
                    <div id="call-arrow" style="
                        display:flex;flex-direction:column;align-items:center;gap:6px;
                        padding:0 8px;
                        opacity:0;
                        transform: scaleX(0.7);
                        transition: opacity 0.5s ease, transform 0.5s ease;
                    ">
                        <div style="font-size:9px;font-weight:600;color:#60a5fa;text-transform:uppercase;letter-spacing:1px;white-space:nowrap;">Sarvam AI</div>
                        <div style="display:flex;align-items:center;gap:0;">
                            <div style="width:22px;height:2px;background:linear-gradient(90deg,transparent,#60a5fa);"></div>
                            <div style="width:0;height:0;border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:8px solid #60a5fa;"></div>
                        </div>
                        <div style="font-size:9px;color:#60a5fa;opacity:0.75;">voice call</div>
                    </div>

                    <!-- ── PHONE 2: Incoming Call Screen ── -->
                    <div id="phone-call" style="
                        width: 220px;
                        opacity: 0;
                        transform: rotateY(18deg) rotateX(6deg) rotateZ(1deg) scale(0.85) translateX(30px);
                        transition: transform 0.9s cubic-bezier(0.34,1.4,0.64,1), opacity 0.7s ease;
                        filter: drop-shadow(0 30px 60px rgba(99,102,241,0.25)) drop-shadow(0 10px 30px rgba(0,0,0,0.6));
                    ">
                        <!-- Phone Frame -->
                        <div style="background:linear-gradient(145deg,#1c1c1e,#2c2c2e);border-radius:36px;padding:10px;border:1px solid rgba(255,255,255,0.08);box-shadow:inset 0 1px 0 rgba(255,255,255,0.1);">
                            <div style="position:relative;background:#000;border-radius:28px;overflow:hidden;min-height:490px;">
                                <!-- Notch -->
                                <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:80px;height:22px;background:#1c1c1e;border-radius:0 0 16px 16px;z-index:10;"></div>

                                <!-- Call Screen Background -->
                                <div style="background:linear-gradient(180deg,#1a1a2e 0%,#16213e 40%,#0f3460 100%);min-height:490px;display:flex;flex-direction:column;align-items:center;padding-top:48px;">

                                    <!-- Incoming call label -->
                                    <div style="color:rgba(255,255,255,0.7);font-size:11px;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">Incoming Call</div>

                                    <!-- Caller avatar with pulsing rings -->
                                    <div style="position:relative;width:88px;height:88px;margin:10px auto;">
                                        <!-- Ring 3 (outermost) -->
                                        <div style="position:absolute;inset:-22px;border-radius:50%;border:1.5px solid rgba(37,211,102,0.2);animation:callRing 2.5s ease-out infinite;animation-delay:0.6s;"></div>
                                        <!-- Ring 2 -->
                                        <div style="position:absolute;inset:-12px;border-radius:50%;border:2px solid rgba(37,211,102,0.3);animation:callRing 2.5s ease-out infinite;animation-delay:0.3s;"></div>
                                        <!-- Ring 1 -->
                                        <div style="position:absolute;inset:-4px;border-radius:50%;border:2px solid rgba(37,211,102,0.5);animation:callRing 2.5s ease-out infinite;"></div>
                                        <!-- Avatar -->
                                        <div style="width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#1e3a5f,#2d6a8f);border:3px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;position:relative;z-index:2;">
                                            <span style="font-size:28px;">🏢</span>
                                        </div>
                                    </div>

                                    <!-- Caller name -->
                                    <div style="color:white;font-size:16px;font-weight:700;margin-top:12px;text-align:center;font-family:'Plus Jakarta Sans',sans-serif;">Pundeer Technologies</div>
                                    <div style="color:rgba(255,255,255,0.5);font-size:11px;margin-top:4px;">Autonomous Voice Agent</div>

                                    <!-- AI tag -->
                                    <div style="margin-top:8px;background:rgba(37,211,102,0.15);border:1px solid rgba(37,211,102,0.3);border-radius:20px;padding:4px 12px;display:flex;align-items:center;gap:5px;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#25d366;display:inline-block;animation:blink 1.2s ease-in-out infinite;"></span>
                                        <span style="color:#25d366;font-size:9px;font-weight:700;letter-spacing:0.5px;">SARVAM AI VOICE AGENT</span>
                                    </div>

                                    <!-- Spacer -->
                                    <div style="flex:1;"></div>

                                    <!-- Swipe hint -->
                                    <div style="color:rgba(255,255,255,0.4);font-size:9px;margin-bottom:12px;">swipe up to answer</div>

                                    <!-- Call action buttons -->
                                    <div style="display:flex;justify-content:space-around;width:100%;padding:0 20px 32px;">
                                        <!-- Decline -->
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                                            <div style="width:52px;height:52px;border-radius:50%;background:#ff3b30;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(255,59,48,0.5);">
                                                <span style="font-size:20px;transform:rotate(135deg);display:inline-block;">📞</span>
                                            </div>
                                            <span style="color:rgba(255,255,255,0.6);font-size:9px;">Decline</span>
                                        </div>

                                        <!-- Accept (pulsing green) -->
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                                            <div style="width:52px;height:52px;border-radius:50%;background:#34c759;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(52,199,89,0.6);animation:answerPulse 1.5s ease-in-out infinite;">
                                                <span style="font-size:20px;">📞</span>
                                            </div>
                                            <span style="color:rgba(255,255,255,0.6);font-size:9px;">Answer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Side buttons -->
                        <div style="position:absolute;right:-3px;top:100px;width:3px;height:40px;background:rgba(255,255,255,0.1);border-radius:0 2px 2px 0;"></div>
                        <div style="position:absolute;left:-3px;top:80px;width:3px;height:28px;background:rgba(255,255,255,0.1);border-radius:2px 0 0 2px;"></div>
                        <div style="position:absolute;left:-3px;top:118px;width:3px;height:28px;background:rgba(255,255,255,0.1);border-radius:2px 0 0 2px;"></div>
                    </div>
                </div>

                <!-- Right: Tech badges (desktop) -->
                <div class="hidden lg:flex flex-col gap-8 w-56" id="demo-steps-right">
                    <div id="demo-badge-1" class="demo-badge-card transition-all duration-500 opacity-40 rounded-xl border p-3 border-transparent" style="background:rgba(37,211,102,0.07);">
                        <div class="font-mono-code text-xs font-semibold mb-1 text-emerald-400">WHATSAPP CLOUD API</div>
                        <div class="text-zinc-400 text-xs">Meta official webhook → intent classifier</div>
                    </div>
                    <div id="demo-badge-2" class="demo-badge-card transition-all duration-500 opacity-40 rounded-xl border p-3 border-transparent" style="background:rgba(167,139,250,0.07);">
                        <div class="font-mono-code text-xs font-semibold mb-1 text-purple-400">RAG RETRIEVAL</div>
                        <div class="text-zinc-400 text-xs">Garlic_Rates_2026.pdf → ₹148/kg grounded</div>
                    </div>
                    <div id="demo-badge-3" class="demo-badge-card transition-all duration-500 opacity-40 rounded-xl border p-3 border-transparent" style="background:rgba(96,165,250,0.07);">
                        <div class="font-mono-code text-xs font-semibold mb-1 text-blue-400">CTA BUTTON</div>
                        <div class="text-zinc-400 text-xs">WhatsApp native call button triggered</div>
                    </div>
                    <div id="demo-badge-4" class="demo-badge-card transition-all duration-500 opacity-40 rounded-xl border p-3 border-transparent" style="background:rgba(52,211,153,0.07);">
                        <div class="font-mono-code text-xs font-semibold mb-1 text-emerald-400">SARVAM AI</div>
                        <div class="text-zinc-400 text-xs">Hindi voice agent dials customer back</div>
                    </div>
                </div>

            </div>

            <!-- Bottom stat row -->
            <div class="mt-14 grid grid-cols-3 gap-6 max-w-lg mx-auto text-center border-t pt-8" style="border-color:rgba(255,255,255,0.08);">
                <div>
                    <div class="font-display text-2xl font-extrabold text-emerald-400">&lt;180ms</div>
                    <div class="text-zinc-500 text-xs mt-1">AI Response</div>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-white">0%</div>
                    <div class="text-zinc-500 text-xs mt-1">Data Leakage</div>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-blue-400">24/7</div>
                    <div class="text-zinc-500 text-xs mt-1">Hindi Voice AI</div>
                </div>
            </div>
        </div>

        <!-- ── Keyframes & Styles ── -->
        <style>
            @keyframes typingDot {
                0%, 60%, 100% { transform: translateY(0); opacity:0.4; }
                30%           { transform: translateY(-4px); opacity:1; }
            }
            @keyframes callRing {
                0%   { transform: scale(0.9); opacity:0.8; }
                100% { transform: scale(1.4); opacity:0; }
            }
            @keyframes answerPulse {
                0%, 100% { box-shadow: 0 4px 20px rgba(52,199,89,0.5); transform: scale(1); }
                50%      { box-shadow: 0 4px 32px rgba(52,199,89,0.9); transform: scale(1.07); }
            }
            @keyframes blink {
                0%, 100% { opacity:1; }
                50%      { opacity:0.3; }
            }
            @keyframes tapPulse {
                0%, 100% { transform: translateY(0) scale(1); }
                50%      { transform: translateY(-3px) scale(1.15); }
            }
            .step-active {
                opacity: 1 !important;
                border-color: rgba(37,211,102,0.3) !important;
                background: rgba(37,211,102,0.08) !important;
                box-shadow: 0 4px 20px rgba(37,211,102,0.12);
            }
        </style>

        <script>
            // ── Interactive Timeline Flow Orchestrator ──
            var demoTimers = [];

            function clearDemoTimers() {
                demoTimers.forEach(function (t) { clearTimeout(t); });
                demoTimers = [];
            }

            function setStepActive(stepNum) {
                for (var i = 1; i <= 4; i++) {
                    var s = document.getElementById('demo-step-' + i);
                    var b = document.getElementById('demo-badge-' + i);
                    if (s) {
                        if (i === stepNum) {
                            s.classList.add('step-active');
                            s.style.opacity = '1';
                        } else if (i < stepNum) {
                            s.classList.remove('step-active');
                            s.style.opacity = '0.7';
                        } else {
                            s.classList.remove('step-active');
                            s.style.opacity = '0.35';
                        }
                    }
                    if (b) {
                        b.style.opacity = (i <= stepNum) ? '1' : '0.35';
                    }
                }
            }

            window.runDemoFlow = function () {
                clearDemoTimers();

                var badge = document.getElementById('demo-status-badge');
                if (badge) badge.textContent = 'Simulating flow...';

                // Reset elements to initial hidden state
                var bCust = document.getElementById('bubble-customer');
                var typing = document.getElementById('typing-indicator');
                var bAi = document.getElementById('bubble-ai');
                var cta = document.getElementById('cta-call-btn');
                var tap = document.getElementById('tap-hint');
                var arrow = document.getElementById('call-arrow');
                var ph1 = document.getElementById('phone-wa');
                var ph2 = document.getElementById('phone-call');
                var ctaInner = document.getElementById('call-cta-inner');

                if (bCust) { bCust.style.opacity = '0'; bCust.style.transform = 'translateX(-20px) scale(0.9)'; }
                if (typing) { typing.style.opacity = '0'; }
                if (bAi) { bAi.style.opacity = '0'; bAi.style.transform = 'translateX(20px) scale(0.9)'; }
                if (cta) { cta.style.opacity = '0'; cta.style.transform = 'translateY(10px) scale(0.9)'; }
                if (tap) { tap.style.opacity = '0'; }
                if (arrow) { arrow.style.opacity = '0'; arrow.style.transform = 'scaleX(0.7)'; }
                if (ph1) {
                    ph1.style.transform = 'rotateY(-16deg) rotateX(6deg) rotateZ(-1deg) translateZ(0px)';
                    ph1.style.filter = 'drop-shadow(0 30px 60px rgba(37,211,102,0.22)) drop-shadow(0 10px 30px rgba(0,0,0,0.6))';
                }
                if (ph2) {
                    ph2.style.opacity = '0';
                    ph2.style.transform = 'rotateY(18deg) rotateX(6deg) rotateZ(1deg) scale(0.85) translateX(30px)';
                }

                // 1. Customer Message arrives (t = 400ms)
                demoTimers.push(setTimeout(function () {
                    setStepActive(1);
                    if (bCust) { bCust.style.opacity = '1'; bCust.style.transform = 'translateX(0) scale(1)'; }
                }, 400));

                // 2. Typing indicator (t = 1400ms)
                demoTimers.push(setTimeout(function () {
                    if (typing) { typing.style.opacity = '1'; }
                }, 1400));

                // 3. AI Reply arrives (t = 2400ms)
                demoTimers.push(setTimeout(function () {
                    setStepActive(2);
                    if (typing) { typing.style.opacity = '0'; }
                    if (bAi) { bAi.style.opacity = '1'; bAi.style.transform = 'translateX(0) scale(1)'; }
                }, 2400));

                // 4. CTA button appears (t = 3600ms)
                demoTimers.push(setTimeout(function () {
                    setStepActive(3);
                    if (cta) { cta.style.opacity = '1'; cta.style.transform = 'translateY(0) scale(1)'; }
                    if (tap) { tap.style.opacity = '1'; }
                }, 3600));

                // 5. Simulated Tap on CTA (t = 4800ms)
                demoTimers.push(setTimeout(function () {
                    if (ctaInner) {
                        ctaInner.style.transform = 'scale(0.92)';
                        setTimeout(function () { ctaInner.style.transform = 'scale(1)'; }, 200);
                    }
                }, 4800));

                // 6. Voice Call arrives on 2nd Phone (t = 5300ms)
                demoTimers.push(setTimeout(function () {
                    setStepActive(4);
                    if (badge) badge.textContent = 'Voice Call Dialed ✓';

                    if (arrow) { arrow.style.opacity = '1'; arrow.style.transform = 'scaleX(1)'; }
                    if (ph1) {
                        ph1.style.transform = 'rotateY(-24deg) rotateX(6deg) rotateZ(-2deg) translateX(-15px)';
                        ph1.style.filter = 'drop-shadow(0 30px 60px rgba(37,211,102,0.12)) drop-shadow(0 10px 30px rgba(0,0,0,0.6)) brightness(0.75)';
                    }
                    if (ph2) {
                        ph2.style.opacity = '1';
                        ph2.style.transform = 'rotateY(16deg) rotateX(6deg) rotateZ(1deg) scale(1) translateX(0px)';
                    }
                }, 5300));
            };

            // Manual CTA click
            function triggerCallScreen() {
                clearDemoTimers();
                setStepActive(4);

                var badge = document.getElementById('demo-status-badge');
                if (badge) badge.textContent = 'Voice Call Dialed ✓';

                var bCust = document.getElementById('bubble-customer');
                var bAi = document.getElementById('bubble-ai');
                var cta = document.getElementById('cta-call-btn');
                var typing = document.getElementById('typing-indicator');
                var arrow = document.getElementById('call-arrow');
                var ph1 = document.getElementById('phone-wa');
                var ph2 = document.getElementById('phone-call');

                if (bCust) { bCust.style.opacity = '1'; bCust.style.transform = 'translateX(0) scale(1)'; }
                if (typing) { typing.style.opacity = '0'; }
                if (bAi) { bAi.style.opacity = '1'; bAi.style.transform = 'translateX(0) scale(1)'; }
                if (cta) { cta.style.opacity = '1'; cta.style.transform = 'translateY(0) scale(1)'; }

                if (arrow) { arrow.style.opacity = '1'; arrow.style.transform = 'scaleX(1)'; }
                if (ph1) {
                    ph1.style.transform = 'rotateY(-24deg) rotateX(6deg) rotateZ(-2deg) translateX(-15px)';
                    ph1.style.filter = 'drop-shadow(0 30px 60px rgba(37,211,102,0.12)) drop-shadow(0 10px 30px rgba(0,0,0,0.6)) brightness(0.75)';
                }
                if (ph2) {
                    ph2.style.opacity = '1';
                    ph2.style.transform = 'rotateY(16deg) rotateX(6deg) rotateZ(1deg) scale(1) translateX(0px)';
                }
            }

            // Scroll trigger with IntersectionObserver
            document.addEventListener('DOMContentLoaded', function () {
                var section = document.getElementById('demo');
                if (!section) return;

                var hasTriggered = false;

                if ('IntersectionObserver' in window) {
                    var observer = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting && entry.intersectionRatio >= 0.2) {
                                if (!hasTriggered) {
                                    hasTriggered = true;
                                    window.runDemoFlow();
                                }
                            } else if (entry.intersectionRatio < 0.05) {
                                // Reset when user scrolls completely away
                                hasTriggered = false;
                            }
                        });
                    }, { threshold: [0.05, 0.2] });

                    observer.observe(section);
                } else {
                    window.runDemoFlow();
                }
            });
        </script>
    </section>

    <!-- Bento Grid Architecture Section -->
    <section id="architecture" class="py-20 bg-[#FAFAFA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <span class="text-xs font-mono-code font-semibold uppercase tracking-wider text-emerald-600">Platform Architecture</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-zinc-950 mt-2 tracking-tight">
                    Built for multi-business founders who hate messy tools.
                </h2>
                <p class="mt-3 text-zinc-600 text-base">
                    Every incoming chat and outbound phone call is routed, sandboxed, and logged into a single clean workspace.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div id="routing" class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Shared Line Intent Routing</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        One Meta WhatsApp Cloud API number serves multiple companies. Our router detects customer intent in under 180ms and locks returning contacts to their assigned business.
                    </p>
                </div>

                <!-- Card 2 -->
                <div id="isolation" class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Zero-Leakage RAG Vaults</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        Upload PDFs, price sheets, or raw notes without even needing a title. Strict server-side tenant filtering ensures Pundeer Tech never quotes Himachal Garlic prices.
                    </p>
                </div>

                <!-- Card 3 -->
                <div id="voice" class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Sarvam AI Voice Telephony</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        Natural Hindi &amp; Hinglish outbound calling with grouped phone history, call duration tracking, AI summaries, audio playback, and one-click CSV exports.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Instant Human Takeover</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        Need to step into a high-value negotiation? Pause the AI agent with one click from the live inbox and reply directly as a human representative.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Follow-Ups &amp; CSV Reports</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        Automatically extract meeting requests, budgets, and callbacks from chats and voice calls. Filter by date range and export 100+ leads to Excel in seconds.
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 hover:border-zinc-300 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-zinc-950">Interactive WhatsApp Flows</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">
                        Send native WhatsApp Reply Buttons, List Menus, and Call-To-Action links automatically based on lead stage and conversation triggers.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dual Business Isolation Showcase -->
    <section class="py-20 bg-white border-y border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-mono-code font-semibold uppercase tracking-wider text-zinc-500">Multi-Tenant Separation</span>
                <h2 class="font-display text-3xl font-extrabold text-zinc-950 mt-2 tracking-tight">
                    Two completely separate businesses. Zero overlap.
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Business A -->
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 p-7">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-xs font-mono-code font-semibold text-blue-600 uppercase">Workspace 01</span>
                            <h3 class="font-display text-xl font-bold text-zinc-950 mt-0.5">Pundeer Technologies</h3>
                        </div>
                        <span class="text-xs font-semibold bg-white border border-zinc-200 px-2.5 py-1 rounded-md text-zinc-700">IT &amp; SaaS</span>
                    </div>
                    <ul class="space-y-2.5 text-sm text-zinc-600">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Chat Agent:</strong> WhatsApp Sales Assistant (Groq / OpenAI)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Voice Agent:</strong> Pundeer IT &amp; Web Voice Agent (Sarvam)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Knowledge Vault:</strong> Web packages, SaaS pricing, CRM specs</span>
                        </li>
                    </ul>
                </div>

                <!-- Business B -->
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 p-7">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-xs font-mono-code font-semibold text-emerald-600 uppercase">Workspace 02</span>
                            <h3 class="font-display text-xl font-bold text-zinc-950 mt-0.5">Himachal Garlic</h3>
                        </div>
                        <span class="text-xs font-semibold bg-white border border-zinc-200 px-2.5 py-1 rounded-md text-zinc-700">Wholesale Agri</span>
                    </div>
                    <ul class="space-y-2.5 text-sm text-zinc-600">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Chat Agent:</strong> Himachal Garlic Assistant</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Voice Agent:</strong> Himachal Garlic Wholesale Voice Desk</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Knowledge Vault:</strong> Grade-A/B mandi rates, dispatch terms</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-[#FAFAFA]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-zinc-950 text-center mb-10 tracking-tight">
                Frequently Asked Questions
            </h2>

            <div class="space-y-3">
                <details class="group bg-white border border-zinc-200 rounded-xl p-5" open>
                    <summary class="flex items-center justify-between font-semibold text-zinc-950 cursor-pointer text-sm sm:text-base">
                        <span>How does 1 WhatsApp number handle two different businesses?</span>
                        <span class="text-zinc-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-zinc-600 leading-relaxed">
                        Incoming messages pass through our Shared Line Intent Router. It checks known contact memory first, then evaluates keywords and intent confidence to route the conversation to the right business workspace without ever mixing knowledge bases.
                    </p>
                </details>

                <details class="group bg-white border border-zinc-200 rounded-xl p-5">
                    <summary class="flex items-center justify-between font-semibold text-zinc-950 cursor-pointer text-sm sm:text-base">
                        <span>How do Sarvam AI voice calls and transcripts work?</span>
                        <span class="text-zinc-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-zinc-600 leading-relaxed">
                        You can trigger manual or automated outbound calls in Hindi/English directly from the Voice Calls dashboard. Calls are grouped by phone number, showing exact timestamps, durations, AI summaries, audio recordings, and full transcripts.
                    </p>
                </details>

                <details class="group bg-white border border-zinc-200 rounded-xl p-5">
                    <summary class="flex items-center justify-between font-semibold text-zinc-950 cursor-pointer text-sm sm:text-base">
                        <span>Can I upload PDFs or spreadsheets into the Knowledge Base?</span>
                        <span class="text-zinc-400 group-open:rotate-180 transition-transform">▾</span>
                    </summary>
                    <p class="mt-3 text-sm text-zinc-600 leading-relaxed">
                        Yes. You can upload PDF, DOCX, TXT, Markdown, and CSV files directly during Knowledge Base creation. Titles are completely optional and automatically derived from your file name.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- Bottom CTA & Footer -->
    <footer class="bg-white border-t border-zinc-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-app-logo-icon class="w-7 h-7 rounded-lg shadow-xs" />
                <span class="font-display font-bold text-sm text-zinc-950">Pundeer AI SalesHub</span>
                <span class="text-xs text-zinc-400">• Multi-Tenant WhatsApp &amp; Voice Suite</span>
            </div>
            <div class="flex items-center gap-6 text-xs font-medium text-zinc-500">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-950">Dashboard</a>
                    <a href="{{ route('admin.calls.index') }}" class="hover:text-zinc-950">Voice Calls</a>
                    <a href="{{ route('admin.knowledge-bases.index') }}" class="hover:text-zinc-950">Knowledge Bases</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-zinc-950">Sign In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="hover:text-zinc-950">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </footer>

</body>
</html>

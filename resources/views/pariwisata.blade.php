<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} &mdash; Pariwisata Kota Padang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --teal: #0d9488;
            --teal-dark: #0f766e;
            --teal-light: #ccfbf1;
            --ocean: #0284c7;
            --ocean-dark: #0369a1;
            --sand: #f59e0b;
            --ink: #0f172a;
            --ink-light: #334155;
            --muted: #64748b;
            --bg: #f1f5f9;
            --card: #ffffff;
            --line: #e2e8f0;
            --line-soft: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Desktop: Tepat 1 layar (No Window Scroll), Chat Input Selalu Terlihat */
        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }
        body {
            display: flex;
            flex-direction: column;
        }

        /* ─── Slim Top Navbar ─── */
        .top-navbar {
            height: 56px;
            background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
            color: #fff;
            padding: 0 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex: none;
            box-shadow: 0 2px 10px rgba(15,23,42,0.15);
            z-index: 50;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .nav-logo {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 10px;
            display: grid;
            place-items: center;
            flex: none;
        }
        .nav-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -.02em;
            line-height: 1.15;
        }
        .nav-subtitle {
            font-size: .72rem;
            opacity: .88;
            font-weight: 400;
        }
        .nav-stats {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .chip {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            padding: .3rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .chip-lokasi {
            cursor: pointer;
            transition: all .15s;
            background: rgba(255,255,255,.22);
        }
        .chip-lokasi:hover {
            background: rgba(255,255,255,.35);
            transform: translateY(-1px);
        }
        .chip-lokasi.aktif {
            background: rgba(16,185,129,.35);
            border-color: rgba(16,185,129,.7);
            color: #ecfdf5;
        }

        /* ─── Main App Layout ─── */
        .layout {
            flex: 1;
            min-height: 0; /* Penting untuk scroll container flexbox */
            display: flex;
            gap: .85rem;
            padding: .75rem 1rem;
            max-width: 1720px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        /* ─── Stage Card (Map + Top Bar + Collapsible Drawer) ─── */
        .stage-card {
            flex: 1;
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 4px 18px -4px rgba(15,23,42,.08);
            overflow: hidden;
        }

        /* Stage Toolbar */
        .stage-bar {
            height: 48px;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 0 .85rem;
            background: #fff;
            border-bottom: 1px solid var(--line);
            flex: none;
            position: relative;
            z-index: 20;
        }

        .drawer-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .7rem;
            border-radius: 8px;
            background: #f1f5f9;
            border: 1px solid var(--line);
            color: var(--ink);
            font-size: .76rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            flex: none;
        }
        .drawer-toggle-btn:hover { background: #e2e8f0; color: var(--teal-dark); }
        .drawer-toggle-btn.active { background: var(--teal-light); border-color: #99f6e4; color: var(--teal-dark); }
        .drawer-toggle-btn .count-badge {
            background: var(--teal);
            color: #fff;
            padding: .05rem .4rem;
            border-radius: 999px;
            font-size: .65rem;
            font-weight: 800;
        }

        .search-box {
            flex: 1;
            min-width: 160px;
            max-width: 260px;
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .32rem .65rem;
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 8px;
            transition: border-color .15s, box-shadow .15s;
        }
        .search-box:focus-within {
            border-color: var(--teal);
            box-shadow: 0 0 0 2px rgba(13,148,136,.12);
            background: #fff;
        }
        .search-box .search-icon { color: var(--muted); font-size: .8rem; }
        .search-box input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font: inherit;
            font-size: .8rem;
            color: var(--ink);
        }
        .search-box input::placeholder { color: #94a3b8; }

        /* Category Filter Chips */
        .filter-chips {
            display: flex;
            gap: .35rem;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            flex: 1;
            min-width: 0;
            padding: .15rem 0;
            scroll-behavior: smooth;
        }
        .filter-chips::-webkit-scrollbar { height: 3px; }
        .filter-chips::-webkit-scrollbar-track { background: transparent; }
        .filter-chips::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .filter-chips .chip {
            white-space: nowrap;
            flex: none;
            cursor: pointer;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--line);
            transition: all .15s ease;
        }
        .filter-chips .chip:hover { background: #e2e8f0; color: var(--ink); border-color: #cbd5e1; }
        .filter-chips .chip.active {
            background: var(--teal);
            color: #fff;
            border-color: var(--teal-dark);
            box-shadow: 0 2px 6px rgba(13,148,136,.25);
        }

        /* Sort Dropdown */
        .sort-wrapper { position: relative; flex: none; }
        .sort-toggle {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: .35rem .65rem;
            cursor: pointer;
            font: inherit;
            font-size: .75rem;
            font-weight: 600;
            color: #475569;
            transition: all .15s;
        }
        .sort-toggle:hover { background: #f1f5f9; color: var(--teal-dark); border-color: #cbd5e1; }
        .sort-menu {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            z-index: 1050;
            width: 200px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: 0 10px 25px -4px rgba(15,23,42,.15);
            padding: .35rem;
            flex-direction: column;
            gap: .15rem;
        }
        .sort-menu.show { display: flex; animation: dropdownIn .15s ease; }
        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .sort-opt {
            text-align: left;
            background: none;
            border: none;
            padding: .4rem .6rem;
            border-radius: 6px;
            font: inherit;
            font-size: .75rem;
            color: var(--ink);
            cursor: pointer;
            transition: background .12s;
        }
        .sort-opt:hover { background: #f1f5f9; }
        .sort-opt.active { background: var(--teal); color: #fff; font-weight: 700; }
        .sort-opt:disabled { color: #94a3b8; cursor: not-allowed; }
        .sort-opt:disabled:hover { background: none; }

        /* Stage Body: Drawer + Map */
        .stage-body {
            flex: 1;
            min-height: 0;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* ─── Places Drawer (Destinasi Wisata) ─── */
        .places-drawer {
            width: 350px;
            flex: none;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-right: 1px solid var(--line);
            transition: margin-left .3s cubic-bezier(.4, 0, .2, 1), opacity .25s ease;
            z-index: 10;
        }
        .places-drawer.collapsed {
            margin-left: -350px;
            pointer-events: none;
            opacity: 0;
        }
        .drawer-header {
            padding: .5rem .8rem;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .72rem;
            color: var(--muted);
            font-weight: 600;
            background: #f8fafc;
            flex: none;
        }
        .drawer-title { font-weight: 700; color: var(--ink); }

        .list-body {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: .5rem .6rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }
        .list-body::-webkit-scrollbar { width: 5px; }
        .list-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .list-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ─── Compact Modern Tourist Card (.wcard) ─── */
        .wcard {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .45rem .55rem;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            cursor: pointer;
            transition: all .15s ease;
        }
        .wcard:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(15,23,42,0.05);
            transform: translateY(-1px);
        }
        .wcard.sorot {
            border-color: var(--teal);
            background: #f0fdfa;
            box-shadow: 0 0 0 2px rgba(13,148,136,.2);
        }
        .wcard-foto-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            overflow: hidden;
            flex: none;
            background: #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .wcard-foto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .wcard-foto.fallback {
            display: grid;
            place-items: center;
            color: var(--teal-dark);
            background: #ccfbf1;
            font-size: .8rem;
            font-weight: 800;
            width: 100%;
            height: 100%;
        }
        .wcard-body {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }
        .wcard-title-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: .3rem;
        }
        .wcard-nama {
            font-size: .82rem;
            font-weight: 700;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wcard-rating {
            font-size: .75rem;
            font-weight: 700;
            color: #d97706;
            flex: none;
        }
        .wcard-info-row {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .72rem;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
        }
        .wcard-kat {
            font-size: .66rem;
            font-weight: 700;
            color: var(--teal-dark);
            background: #ccfbf1;
            padding: .08rem .4rem;
            border-radius: 4px;
            flex: none;
        }
        .wcard-tiket { font-weight: 600; color: #334155; flex: none; }
        .wcard-jarak { color: var(--ocean-dark); font-weight: 700; flex: none; }

        .wcard-rute-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #f1f5f9;
            border: 1px solid var(--line);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: .85rem;
            color: var(--teal-dark);
            flex: none;
            transition: all .15s;
        }
        .wcard-rute-btn:hover {
            background: var(--teal);
            border-color: var(--teal-dark);
            color: #fff;
            transform: scale(1.08);
        }
        .wcard-rute-btn.aktif {
            background: var(--ocean);
            border-color: #0369a1;
            color: #fff;
            animation: routePulse 2s ease-in-out infinite;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
            padding: 2rem 1rem;
            color: var(--muted);
            font-size: .8rem;
            text-align: center;
        }
        .empty-state .icon { font-size: 2rem; opacity: .6; }
        .btn-reset-filter {
            background: #f1f5f9;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: .3rem .65rem;
            font: inherit;
            font-size: .75rem;
            font-weight: 600;
            color: var(--teal-dark);
            cursor: pointer;
            transition: background .15s;
        }
        .btn-reset-filter:hover { background: #e2e8f0; }

        /* Skeleton shimmer */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 50%, #e2e8f0 100%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ─── Map Wrapper ─── */
        .map-wrapper {
            flex: 1;
            height: 100%;
            min-width: 0;
            position: relative;
        }
        #map {
            width: 100%;
            height: 100%;
        }
        .map-helper-badge {
            position: absolute;
            bottom: 12px;
            left: 12px;
            z-index: 1000;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(226,232,240,.9);
            border-radius: 7px;
            padding: .25rem .55rem;
            font-size: .68rem;
            font-weight: 600;
            color: var(--muted);
            pointer-events: none;
            box-shadow: 0 2px 8px rgba(15,23,42,.08);
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ─── Chatbot Card (AI Travel Concierge) ─── */
        .chat-card {
            width: 380px;
            flex: none;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 4px 18px -4px rgba(15,23,42,.08);
            overflow: hidden;
        }
        .chat-head {
            height: 48px;
            padding: 0 .9rem;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: none;
        }
        .concierge-brand {
            display: flex;
            align-items: center;
            gap: .55rem;
        }
        .ai-live-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16,185,129,.25);
            animation: livePulse 2s infinite;
        }
        @keyframes livePulse {
            0%, 100% { box-shadow: 0 0 0 2px rgba(16,185,129,.25); }
            50%      { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
        }
        .concierge-title {
            font-size: .84rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.1;
        }
        .concierge-sub {
            font-size: .65rem;
            color: var(--muted);
            font-weight: 500;
        }

        .chat-body {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: .75rem;
            gap: .5rem;
        }

        /* Messages Area — Scrolls independently */
        .messages {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: .6rem;
            overflow-y: auto;
            padding-right: .2rem;
        }
        .messages::-webkit-scrollbar { width: 4px; }
        .messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .msg { display: flex; gap: .5rem; animation: msgIn .3s ease both; }
        .msg.user { flex-direction: row-reverse; animation-name: msgInRight; }
        @keyframes msgIn {
            from { opacity: 0; transform: translateX(-10px) translateY(4px); }
            to   { opacity: 1; transform: translateX(0) translateY(0); }
        }
        @keyframes msgInRight {
            from { opacity: 0; transform: translateX(10px) translateY(4px); }
            to   { opacity: 1; transform: translateX(0) translateY(0); }
        }

        .avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--teal), var(--ocean));
            color: #fff;
            display: grid;
            place-items: center;
            font-size: .65rem;
            font-weight: 800;
            flex: none;
            box-shadow: 0 2px 5px rgba(13,148,136,.2);
        }
        .avatar.user-av { background: linear-gradient(135deg, var(--sand), #ef4444); }
        .avatar.thinking {
            animation: avatarThink 1.2s linear infinite;
            background: linear-gradient(135deg, var(--teal), var(--ocean), var(--sand), var(--teal));
            background-size: 300% 300%;
        }
        @keyframes avatarThink {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .bubble {
            background: #f8fafc;
            border: 1px solid var(--line);
            padding: .55rem .8rem;
            border-radius: 4px 12px 12px 12px;
            font-size: .82rem;
            line-height: 1.5;
            color: #334155;
            max-width: 88%;
        }
        .bubble.user-bubble {
            background: var(--teal);
            border-color: var(--teal-dark);
            color: #fff;
            border-radius: 12px 4px 12px 12px;
        }
        .bubble.reveal { animation: bubbleReveal .25s ease both; }
        @keyframes bubbleReveal {
            from { opacity: 0; transform: scale(.97); }
            to   { opacity: 1; transform: scale(1); }
        }
        .bubble strong { color: var(--teal-dark); }
        .bubble.user-bubble strong { color: #ccfbf1; }
        .bubble p { margin: .25rem 0; }
        .bubble p:first-child { margin-top: 0; }
        .bubble p:last-child { margin-bottom: 0; }

        .typing { display: flex; gap: 4px; align-items: center; padding: .35rem .5rem; }
        .typing span { width: 6px; height: 6px; border-radius: 50%; background: var(--teal); animation: typingDot 1.1s ease-in-out infinite; }
        .typing span:nth-child(2) { animation-delay: .2s; background: var(--ocean); }
        .typing span:nth-child(3) { animation-delay: .4s; background: var(--sand); }
        @keyframes typingDot {
            0%, 60%, 100% { transform: translateY(0) scale(1); opacity: .5; }
            30%            { transform: translateY(-4px) scale(1.2); opacity: 1; }
        }

        /* Hint Prompts — Compact 2x2 Grid (Hanya memakan ~44px, tidak mendorong input ke bawah) */
        .hint {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .3rem;
            flex: none;
        }
        .hint button {
            text-align: left;
            font: inherit;
            font-size: .7rem;
            padding: .32rem .55rem;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #f8fafc;
            color: #334155;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .hint button:hover {
            background: #f0fdfa;
            border-color: var(--teal);
            color: var(--teal-dark);
        }
        .hint button:disabled { cursor: not-allowed; opacity: .5; }
        .hint.fade-out { opacity: 0; pointer-events: none; transition: opacity .2s; }

        /* Input Area — SELALU TERLIHAT di bagian bawah chat card */
        .input {
            display: flex;
            gap: .4rem;
            flex: none;
        }
        .input input {
            flex: 1;
            padding: .55rem .75rem;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #f8fafc;
            font: inherit;
            font-size: .82rem;
            color: var(--ink);
            outline: none;
            transition: border-color .15s;
        }
        .input input:focus { border-color: var(--teal); background: #fff; }
        .input input:disabled { cursor: not-allowed; color: var(--muted); background: #f1f5f9; }
        .send-btn {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .55rem .85rem;
            border-radius: 10px;
            border: none;
            background: var(--teal);
            color: #fff;
            font: inherit;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, transform .15s;
            flex: none;
        }
        .send-btn:hover:not(:disabled) { background: var(--teal-dark); transform: translateY(-1px); }
        .send-btn:active:not(:disabled) { transform: scale(.96); }
        .send-btn:disabled { opacity: .5; cursor: not-allowed; }

        .status-bar {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .74rem;
            font-weight: 600;
            padding: .35rem .6rem;
            border-radius: 8px;
            transition: opacity .4s ease;
            flex: none;
        }
        .status-bar.warning { color: #92400e; background: #fef3c7; border: 1px solid #fcd34d; }
        .status-bar.success { color: #065f46; background: #d1fae5; border: 1px solid #6ee7b7; }
        .status-bar.error-bar { color: #991b1b; background: #fee2e2; border: 1px solid #fca5a5; }

        /* Inside Chat: Tourist Recommendation Cards */
        .wisata-cards {
            display: flex;
            flex-direction: column;
            gap: .45rem;
            margin-top: .6rem;
        }
        .wchat-card {
            display: flex;
            gap: .55rem;
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .5rem .65rem;
            cursor: pointer;
            transition: all .18s cubic-bezier(.4, 0, .2, 1);
            box-shadow: 0 1px 3px rgba(15,23,42,0.04);
        }
        .wchat-card:hover {
            border-color: var(--teal);
            background: #f0fdfa;
            box-shadow: 0 4px 12px rgba(13,148,136,.14);
            transform: translateY(-1px);
        }
        .wchat-card.bermasalah { border-color: #fca5a5; background: #fff5f5; }
        .wchat-card.bermasalah:hover { border-color: #ef4444; background: #fef2f2; }

        .wchat-num {
            width: 22px;
            height: 22px;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--teal), var(--ocean));
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            display: grid;
            place-items: center;
            flex: none;
            box-shadow: 0 2px 5px rgba(13,148,136,.25);
        }
        .wchat-info { flex: 1; min-width: 0; }
        .wchat-nama {
            font-weight: 700;
            font-size: .82rem;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wchat-meta {
            font-size: .72rem;
            color: #475569;
            margin-top: .12rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wchat-actions {
            display: flex;
            gap: .3rem;
            align-items: center;
            flex: none;
        }
        .wchat-rute {
            background: var(--teal);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: .3rem .55rem;
            font: inherit;
            font-size: .7rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            transition: all .15s ease;
        }
        .wchat-rute:hover { background: var(--teal-dark); transform: scale(1.03); }
        .wchat-rute.aktif { background: var(--ocean); animation: routePulse 2s ease-in-out infinite; }
        .wchat-gmaps {
            background: #f1f5f9;
            color: #0369a1;
            border: 1px solid #bae6fd;
            border-radius: 7px;
            padding: .3rem .55rem;
            font: inherit;
            font-size: .7rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            transition: all .15s ease;
        }
        .wchat-gmaps:hover { background: #e0f2fe; border-color: #7dd3fc; }

        @keyframes routePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(3,105,161,.4); }
            50%       { box-shadow: 0 0 0 5px rgba(3,105,161,0); }
        }

        /* Leaflet Popup */
        .leaflet-popup-content-wrapper {
            border-radius: 14px !important;
            box-shadow: 0 12px 32px -4px rgba(15,23,42,.22), 0 0 0 1px rgba(15,23,42,.06) !important;
            padding: 0 !important;
            overflow: hidden;
        }
        .leaflet-popup-content {
            width: 295px !important;
            margin: .85rem !important;
            line-height: 1.5;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .pop-foto {
            width: 100%;
            height: 125px;
            object-fit: cover;
            border-radius: 9px;
            margin-bottom: .5rem;
            background: #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .pop-foto.fallback {
            display: grid;
            place-items: center;
            color: var(--teal-dark);
            background: #ccfbf1;
            font-size: .8rem;
            font-weight: 700;
            height: 65px;
        }
        .pop-nama {
            font-weight: 800;
            font-size: .95rem;
            color: var(--ink);
            letter-spacing: -.01em;
        }
        .pop-kat {
            display: inline-block;
            margin-top: .2rem;
            font-size: .68rem;
            font-weight: 700;
            color: var(--teal-dark);
            background: #ccfbf1;
            padding: .12rem .5rem;
            border-radius: 999px;
        }
        .pop-row {
            margin-top: .35rem;
            font-size: .74rem;
            color: #475569;
        }
        .pop-desc {
            font-size: .73rem;
            color: #334155;
            margin-top: .4rem;
            line-height: 1.45;
            max-height: 70px;
            overflow-y: auto;
            padding: .35rem .45rem;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid var(--line);
        }
        .pop-desc::-webkit-scrollbar { width: 3px; }
        .pop-desc::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .pop-jarak { color: var(--ocean-dark); font-weight: 700; }
        .pop-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: .4rem;
            margin-top: .65rem;
        }
        .pop-actions a {
            font-size: .72rem;
            padding: .35rem .5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .25rem;
            transition: all .15s ease;
        }
        .pop-call { background: var(--teal); color: #fff; }
        .pop-call:hover { background: var(--teal-dark); }
        .pop-gmaps { background: #0284c7; color: #fff; }
        .pop-gmaps:hover { background: #0369a1; }
        .pop-map { background: #f1f5f9; color: #334155; border: 1px solid var(--line); }
        .pop-map:hover { background: #e2e8f0; }

        /* Badges */
        .badge-cuaca { display: inline-flex; align-items: center; gap: .2rem; font-size: .66rem; font-weight: 600; padding: .12rem .5rem; border-radius: 999px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; margin-top: .2rem; }
        .badge-cuaca.buruk { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .badge-status { display: inline-flex; align-items: center; gap: .2rem; font-size: .66rem; font-weight: 700; padding: .12rem .5rem; border-radius: 999px; margin-top: .2rem; }
        .badge-status.normal        { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-status.tutup_sementara { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-status.renovasi      { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-status.banjir        { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-status.longsor       { background: #fdf4ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-status.akses_terbatas { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }

        /* Floating Button Mobile & Tab Bar */
        .mobile-nav-bar { display: none; }
        .fab-chat { display: none !important; }

        /* ─── Responsive Breakpoint: Layar Kecil / HP (Tab-Based Experience) ─── */
        @media (max-width: 1024px) {
            html, body {
                height: 100dvh;
                overflow: hidden;
            }
            body {
                height: 100dvh;
            }
            .top-navbar {
                height: 52px;
                padding: 0 .85rem;
            }
            .nav-title { font-size: .95rem; }
            .nav-subtitle { display: none; }
            .nav-stats .chip:not(#chip-lokasi) { display: none; }
            .chip-lokasi { font-size: .72rem; padding: .25rem .6rem; }

            .layout {
                flex: 1;
                min-height: 0;
                padding: .4rem .4rem 0;
                gap: 0;
                position: relative;
            }

            /* View 1: Peta (Default) */
            body.mobile-view-peta .stage-card { display: flex; width: 100%; height: 100%; }
            body.mobile-view-peta .places-drawer { display: none !important; }
            body.mobile-view-peta .map-wrapper { flex: 1; height: 100%; }
            body.mobile-view-peta .chat-card { display: none !important; }

            /* View 2: Destinasi */
            body.mobile-view-destinasi .stage-card { display: flex; width: 100%; height: 100%; }
            body.mobile-view-destinasi .places-drawer {
                display: flex !important;
                width: 100% !important;
                margin-left: 0 !important;
                border-right: none;
                flex: 1;
                height: 100%;
                opacity: 1 !important;
                pointer-events: auto !important;
            }
            body.mobile-view-destinasi .map-wrapper { display: none !important; }
            body.mobile-view-destinasi .chat-card { display: none !important; }

            /* View 3: Chat AI */
            body.mobile-view-chat .stage-card { display: none !important; }
            body.mobile-view-chat .chat-card {
                display: flex !important;
                width: 100%;
                height: 100%;
            }

            /* Responsive stage-bar on mobile */
            .stage-bar {
                height: auto;
                min-height: 46px;
                padding: .4rem .55rem;
                flex-wrap: wrap;
                gap: .4rem;
            }
            .drawer-toggle-btn { display: none; }
            .search-box {
                flex: 1 1 170px;
                max-width: none;
            }
            .sort-wrapper { order: 2; flex: none; }
            .filter-chips {
                order: 3;
                width: 100%;
                padding: .2rem 0;
            }

            /* Mobile Bottom Navigation Bar */
            .mobile-nav-bar {
                display: flex;
                height: 56px;
                background: #fff;
                border-top: 1px solid var(--line);
                box-shadow: 0 -4px 16px rgba(15,23,42,0.06);
                z-index: 1000;
                flex: none;
                justify-content: space-around;
                align-items: center;
                padding: 0 .5rem;
            }
            .mobile-tab-btn {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: .15rem;
                background: none;
                border: none;
                color: var(--muted);
                font: inherit;
                font-size: .68rem;
                font-weight: 700;
                cursor: pointer;
                padding: .35rem 0;
                border-radius: 8px;
                transition: all .15s ease;
                position: relative;
            }
            .mobile-tab-btn.active {
                color: var(--teal-dark);
            }
            .mobile-tab-btn.active .tab-icon-wrap {
                background: var(--teal-light);
                color: var(--teal-dark);
                transform: translateY(-2px);
            }
            .tab-icon-wrap {
                width: 34px;
                height: 24px;
                display: grid;
                place-items: center;
                border-radius: 999px;
                font-size: 1rem;
                transition: all .15s ease;
            }
            .mobile-badge {
                position: absolute;
                top: 2px;
                right: calc(50% - 18px);
                background: var(--teal);
                color: #fff;
                font-size: .6rem;
                font-weight: 800;
                padding: .05rem .35rem;
                border-radius: 999px;
            }
            .mobile-badge.dot {
                width: 8px;
                height: 8px;
                padding: 0;
                background: #10b981;
                right: calc(50% - 12px);
            }
        }
    </style>
</head>
<body>
    <!-- Top Slim Navbar -->
    <header class="top-navbar">
        <div class="nav-brand">
            <div class="nav-logo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
                <h1 class="nav-title">{{ config('app.name') }}</h1>
                <div class="nav-subtitle">Sistem Rekomendasi Pariwisata Padang berbasis SQL Grounding</div>
            </div>
        </div>
        <div class="nav-stats">
            <span class="chip">{{ $wisata->count() }} Destinasi</span>
            <span class="chip" title="Rating rata-rata">⭐ {{ number_format((float) $wisata->avg('rating'), 1) }}</span>
            <span class="chip chip-lokasi" id="chip-lokasi" onclick="mintaLokasi()" title="Aktifkan GPS">
                📍 Izinkan Lokasi
            </span>
        </div>
    </header>

    <!-- Main Viewport Layout -->
    <main class="layout">
        <!-- STAGE CARD: Map + Toolbar + Collapsible Drawer -->
        <section class="stage-card">
            <div class="stage-bar">
                <button class="drawer-toggle-btn active" id="drawer-toggle" onclick="toggleDrawer()" title="Sembunyikan/Tampilkan Destinasi">
                    <span id="drawer-toggle-icon">◀</span>
                    <span id="drawer-toggle-text">Daftar Destinasi</span>
                    <span class="count-badge" id="drawer-count">{{ $wisata->count() }}</span>
                </button>

                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="search" id="search-input" placeholder="Cari pantai, museum, kuliner..." autocomplete="off">
                </div>

                <div class="filter-chips" id="filter-chips">
                    <button class="chip active" data-kat="*">Semua</button>
                    <button class="chip" data-kat="Pantai">🏖️ Pantai</button>
                    <button class="chip" data-kat="Pulau">🏝️ Pulau</button>
                    <button class="chip" data-kat="Alam">🌲 Alam</button>
                    <button class="chip" data-kat="Museum">🏛️ Museum</button>
                    <button class="chip" data-kat="Sejarah">⛩️ Sejarah</button>
                    <button class="chip" data-kat="Kuliner">🍽️ Kuliner</button>
                </div>

                <div class="sort-wrapper">
                    <button class="sort-toggle" id="sort-toggle" title="Urutkan destinasi" aria-label="Urutkan">
                        <span>⇅ Urutkan</span>
                    </button>
                    <div class="sort-menu" id="sort-menu">
                        <button class="sort-opt active" data-sort="rating">⭐ Rating tertinggi</button>
                        <button class="sort-opt" data-sort="name-asc">🔤 Nama A–Z</button>
                        <button class="sort-opt" data-sort="name-desc">🔤 Nama Z–A</button>
                        <button class="sort-opt" data-sort="price-asc">💰 Termurah dulu</button>
                        <button class="sort-opt" data-sort="price-desc">💎 Termahal dulu</button>
                        <button class="sort-opt" data-sort="jarak" id="sort-jarak" disabled>📍 Jarak (izin lokasi dulu)</button>
                    </div>
                </div>
            </div>

            <div class="stage-body">
                <!-- Places Drawer (350px width, clean micro cards) -->
                <aside class="places-drawer" id="list-card">
                    <div class="drawer-header">
                        <span class="drawer-title">Destinasi Pilihan</span>
                        <span id="list-count-label">{{ $wisata->count() }} tempat</span>
                    </div>
                    <div class="list-body" id="list-body">
                        {{-- Diisi via JavaScript renderList() --}}
                    </div>
                </aside>

                <!-- Full Stage Map -->
                <div class="map-wrapper">
                    <div id="map"></div>
                    <div class="map-helper-badge">
                        <span>🗺️</span> Klik marker untuk detail &amp; rute OSRM
                    </div>
                </div>
            </div>
        </section>

        <!-- CHAT CARD: AI Concierge (Always Visible Input at Bottom) -->
        <aside class="chat-card">
            <div class="chat-head">
                <div class="concierge-brand">
                    <div class="ai-live-dot"></div>
                    <div>
                        <div class="concierge-title">Padang AI Concierge</div>
                        <div class="concierge-sub">SQL Grounded &middot; Asli Kota Padang</div>
                    </div>
                </div>
            </div>
            <div class="chat-body">
                <!-- Message Container (Internal Scroll) -->
                <div class="messages" id="messages">
                    <div class="msg">
                        <div class="avatar">AI</div>
                        <div class="bubble">Halo! Saya asisten wisata Kota Padang. Ada destinasi atau rute yang ingin Anda tanyakan?</div>
                    </div>
                </div>

                <!-- Compact Hint Buttons (Hanya 44px tinggi) -->
                <div class="hint" id="hint">
                    <button onclick="kirimHint(this)">🏖️ Pantai terdekat</button>
                    <button onclick="kirimHint(this)">🕐 Buka sekarang</button>
                    <button onclick="kirimHint(this)">🍽️ Kuliner khas Padang</button>
                    <button onclick="kirimHint(this)">🏛️ Museum keluarga</button>
                </div>

                <!-- Input & Send Button (Permanen di Bawah, Langsung Terlihat) -->
                <div class="input">
                    <input type="text" id="input-pesan" placeholder="Tanya rekomendasi wisata..." autocomplete="off">
                    <button class="send-btn" id="send-btn" onclick="kirimPesan()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        <span>Kirim</span>
                    </button>
                </div>

                <div class="status-bar warning" id="status-bar">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span id="status-text">Memulai sesi...</span>
                </div>
            </div>
        </aside>
    </main>

    <!-- Mobile Bottom Navigation Bar (Presisi & Tanpa Gestures Trap) -->
    <nav class="mobile-nav-bar" id="mobile-nav-bar">
        <button class="mobile-tab-btn active" data-view="peta" onclick="switchMobileView('peta')">
            <span class="tab-icon-wrap">🗺️</span>
            <span>Peta</span>
        </button>
        <button class="mobile-tab-btn" data-view="destinasi" onclick="switchMobileView('destinasi')">
            <span class="tab-icon-wrap">📋</span>
            <span>Destinasi</span>
            <span class="mobile-badge" id="mobile-destinasi-badge">{{ $wisata->count() }}</span>
        </button>
        <button class="mobile-tab-btn" data-view="chat" onclick="switchMobileView('chat')">
            <span class="tab-icon-wrap">💬</span>
            <span>Tanya AI</span>
            <span class="mobile-badge dot" id="mobile-chat-dot"></span>
        </button>
    </nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    // ─── State ───────────────────────────────────────────────────────────────
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    let sessionToken = localStorage.getItem('chat_session_token') || null;
    let userLat = null, userLng = null;
    let userMarker = null;
    let ruteLayer  = null;
    let sedangKirim = false;

    // ─── Peta Leaflet ────────────────────────────────────────────────────────
    const map = L.map('map').setView([-0.9471, 100.4174], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker semua wisata dari server
    const wisataData = @json($wisata);
    const wisataMarkers = {};

    // ─── Drawer Toggle ───────────────────────────────────────────────────────
    window.toggleDrawer = function() {
        const drawer = document.getElementById('list-card');
        const toggleBtn = document.getElementById('drawer-toggle');
        if (!drawer) return;

        const isCollapsed = drawer.classList.toggle('collapsed');
        if (toggleBtn) {
            const icon = document.getElementById('drawer-toggle-icon');
            const text = document.getElementById('drawer-toggle-text');
            if (icon) icon.textContent = isCollapsed ? '▶' : '◀';
            if (text) text.textContent = isCollapsed ? 'Buka Destinasi' : 'Daftar Destinasi';
            toggleBtn.classList.toggle('active', !isCollapsed);
        }

        setTimeout(() => {
            map.invalidateSize();
        }, 320);
    };

    // ─── Sidebar list & filter chips ─────────────────────────────────────────
    const listBody = document.getElementById('list-body');
    const filterChips = document.getElementById('filter-chips');
    const sortMenu = document.getElementById('sort-menu');
    const sortToggle = document.getElementById('sort-toggle');
    const searchInput = document.getElementById('search-input');
    let aktifFilter = '*';
    let aktifSort = 'rating';
    let searchQuery = '';

    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function applyFilterSortSearch() {
        let list = [...wisataData];
        if (aktifFilter !== '*') {
            list = list.filter(w => w.kategori.nama === aktifFilter);
        }
        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            list = list.filter(w =>
                w.nama.toLowerCase().includes(q) ||
                (w.alamat || '').toLowerCase().includes(q) ||
                (w.deskripsi || '').toLowerCase().includes(q) ||
                w.kategori.nama.toLowerCase().includes(q)
            );
        }
        if (aktifSort === 'rating') list.sort((a, b) => Number(b.rating) - Number(a.rating));
        else if (aktifSort === 'name-asc') list.sort((a, b) => a.nama.localeCompare(b.nama));
        else if (aktifSort === 'name-desc') list.sort((a, b) => b.nama.localeCompare(a.nama));
        else if (aktifSort === 'price-asc') list.sort((a, b) => Number(a.harga_tiket) - Number(b.harga_tiket));
        else if (aktifSort === 'price-desc') list.sort((a, b) => Number(b.harga_tiket) - Number(a.harga_tiket));
        else if (aktifSort === 'jarak' && userLat && userLng) {
            list.forEach(w => w._jarak = haversine(userLat, userLng, Number(w.lat), Number(w.lng)));
            list.sort((a, b) => a._jarak - b._jarak);
        }
        return list;
    }

    function renderList() {
        const list = applyFilterSortSearch();
        const countLabel = document.getElementById('list-count-label');
        if (countLabel) countLabel.textContent = `${list.length} tempat`;
        const drawerCount = document.getElementById('drawer-count');
        if (drawerCount) drawerCount.textContent = list.length;
        const mobBadge = document.getElementById('mobile-destinasi-badge');
        if (mobBadge) mobBadge.textContent = list.length;

        if (list.length === 0) {
            listBody.innerHTML = `
                <div class="empty-state">
                    <div class="icon">🗺️</div>
                    <div>Tidak ada destinasi yang cocok dengan pencarian "${searchQuery || aktifFilter}".</div>
                    <button class="btn-reset-filter" onclick="document.getElementById('search-input').value=''; window.dispatchEvent(new Event('clear-search'));">Reset Pencarian</button>
                </div>`;
            return;
        }

        listBody.innerHTML = list.map(w => {
            const katInitial = (w.kategori && w.kategori.nama) ? w.kategori.nama[0] : '📍';
            const katNama = (w.kategori && w.kategori.nama) ? w.kategori.nama : (w.kategori || 'Wisata');
            const fotoHtml = w.foto
                ? `<div class="wcard-foto-wrap skeleton"><img class="wcard-foto" src="${w.foto}" alt="${w.nama}" loading="lazy" onload="this.parentElement.classList.remove('skeleton')" onerror="this.parentElement.classList.remove('skeleton'); this.outerHTML='<div class=\\'wcard-foto fallback\\'>${katInitial}</div>'"></div>`
                : `<div class="wcard-foto-wrap"><div class="wcard-foto fallback">${katInitial}</div></div>`;
            const tiket = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
            const jarak = w._jarak !== undefined ? ` · 📍 ${w._jarak.toFixed(1)} km` : '';
            const status = w.status_operasional || 'normal';
            const [stIkon, stLabel] = statusLabel(status);
            const statusBadge = status !== 'normal'
                ? `<span class="badge-status ${status}" style="font-size:.62rem;padding:.05rem .3rem">${stIkon} ${stLabel}</span>`
                : '';

            return `
                <div class="wcard" data-id="${w.id}" onclick="fokusWisata(${w.id})" title="Klik untuk lihat di peta">
                    ${fotoHtml}
                    <div class="wcard-body">
                        <div class="wcard-title-row">
                            <span class="wcard-nama">${w.nama}</span>
                            <span class="wcard-rating">⭐ ${Number(w.rating).toFixed(1)}</span>
                        </div>
                        <div class="wcard-info-row">
                            <span class="wcard-kat">${katNama}</span>
                            <span class="wcard-tiket">${tiket}</span>
                            ${jarak ? `<span class="wcard-jarak">${jarak}</span>` : ''}
                            ${statusBadge}
                        </div>
                    </div>
                    <button class="wcard-rute-btn" onclick="event.stopPropagation(); ruteKeWisata(${Number(w.lat)}, ${Number(w.lng)}, '${w.nama.replace(/'/g,"\\'")}', this)" title="Rute ke ${w.nama}">
                        <span>🗺️</span>
                    </button>
                </div>`;
        }).join('');
    }

    function statusLabel(status) {
        const map = {
            normal:           ['✅', 'Beroperasi Normal'],
            tutup_sementara:  ['🔒', 'Tutup Sementara'],
            renovasi:         ['🚧', 'Sedang Renovasi'],
            banjir:           ['🌊', 'Terdampak Banjir'],
            longsor:          ['⛰️', 'Terdampak Longsor'],
            akses_terbatas:   ['⚠️', 'Akses Terbatas'],
        };
        return map[status] || ['❓', status];
    }

    function popupHtml(w, idx = null) {
        const nomor = idx !== null ? `${idx + 1}. ` : '';
        const katName = (typeof w.kategori === 'object' && w.kategori) ? w.kategori.nama : (w.kategori || 'Wisata');
        const katInitial = katName ? katName[0] : '📍';
        const fotoHtml = w.foto
            ? `<img class="pop-foto" src="${w.foto}" alt="${w.nama}" loading="lazy" onerror="this.outerHTML='<div class=\\'pop-foto fallback\\'>${katInitial}</div>'">`
            : `<div class="pop-foto fallback">${katInitial}</div>`;
        const tiket = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
        const telp  = w.telepon ? `<a href="tel:${w.telepon}" class="pop-call" title="Hubungi pengelola">📞 Hubungi</a>` : '';
        const gmaps = `<a href="https://www.google.com/maps/dir/?api=1&destination=${w.lat},${w.lng}" target="_blank" rel="noopener" class="pop-gmaps" title="Navigasi langsung via Google Maps">🚗 G-Maps</a>`;
        const osm   = `<a href="https://www.openstreetmap.org/?mlat=${w.lat}&mlon=${w.lng}#map=17/${w.lat}/${w.lng}" target="_blank" rel="noopener" class="pop-map" title="Buka di OpenStreetMap">🗺️ OSM</a>`;

        const cuaca = w.cuaca;
        const cuacaHtml = cuaca
            ? `<div><span class="badge-cuaca ${cuaca.buruk ? 'buruk' : ''}">${cuaca.emoji} ${cuaca.label} · ${cuaca.suhu}°C</span></div>`
            : '';

        const status = w.status_operasional || 'normal';
        const [stIkon, stLabel] = statusLabel(status);
        const statusHtml = `<div><span class="badge-status ${status}">${stIkon} ${stLabel}</span>${w.catatan_status ? `<span style="font-size:.65rem;color:#64748b;margin-left:.3rem">${w.catatan_status}</span>` : ''}</div>`;

        return `
            ${fotoHtml}
            <div class="pop-nama">${nomor}${w.nama}</div>
            <div><span class="pop-kat">${katName}</span></div>
            ${cuacaHtml}
            ${statusHtml}
            <div class="pop-row">📍 ${w.alamat || '-'}</div>
            <div class="pop-row">🎫 ${tiket} &middot; ⭐ ${Number(w.rating).toFixed(1)}</div>
            <div class="pop-row">🕐 ${w.jam_buka ? w.jam_buka.slice(0,5) : '-'}–${w.jam_tutup ? w.jam_tutup.slice(0,5) : '-'} WIB</div>
            ${w.jarak_km !== undefined ? `<div class="pop-row pop-jarak">🚗 ${w.jarak_km} km dari lokasimu</div>` : ''}
            ${w.deskripsi ? `<div class="pop-desc">${w.deskripsi}</div>` : ''}
            <div class="pop-actions">${telp}${gmaps}${osm}</div>
        `;
    }

    // Navigasi Tab Mobile
    window.switchMobileView = function(viewName) {
        document.body.classList.remove('mobile-view-peta', 'mobile-view-destinasi', 'mobile-view-chat');
        document.body.classList.add(`mobile-view-${viewName}`);

        document.querySelectorAll('.mobile-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === viewName);
        });

        if (viewName === 'peta') {
            setTimeout(() => { map.invalidateSize(); }, 200);
        } else if (viewName === 'chat') {
            setTimeout(() => {
                const messages = document.getElementById('messages');
                if (messages) messages.scrollTop = messages.scrollHeight;
                const input = document.getElementById('input-pesan');
                if (input && window.innerWidth <= 1024) input.focus();
            }, 150);
        }
    };

    // Fokus ke wisata dari klik list/chat
    window.fokusWisata = function(id) {
        if (window.innerWidth <= 1024) {
            switchMobileView('peta');
        }
        const marker = wisataMarkers[id];
        if (!marker) return;
        map.setView(marker.getLatLng(), 15, { animate: true });
        marker.openPopup();
        document.querySelectorAll('.wcard').forEach(el => el.classList.remove('sorot'));
        const card = document.querySelector(`.wcard[data-id="${id}"]`);
        if (card) {
            card.classList.add('sorot');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // Mobile chat scroll helper
    window.scrollKeChat = function() {
        const chatCard = document.querySelector('.chat-card');
        if (chatCard) {
            chatCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(() => {
                const input = document.getElementById('input-pesan');
                if (input) input.focus();
            }, 500);
        }
    };

    filterChips.addEventListener('click', e => {
        const btn = e.target.closest('.chip');
        if (!btn) return;
        aktifFilter = btn.dataset.kat;
        filterChips.querySelectorAll('.chip').forEach(c => c.classList.toggle('active', c === btn));
        renderList();
    });

    sortToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        sortMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => {
        sortMenu.classList.remove('show');
    });

    sortMenu.addEventListener('click', e => {
        const btn = e.target.closest('.sort-opt');
        if (!btn || btn.disabled) return;
        aktifSort = btn.dataset.sort;
        sortMenu.querySelectorAll('.sort-opt').forEach(b => b.classList.toggle('active', b === btn));
        sortMenu.classList.remove('show');
        renderList();
    });

    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim();
        renderList();
    });

    window.addEventListener('clear-search', () => {
        searchQuery = '';
        searchInput.value = '';
        renderList();
    });

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });

    renderList();

    wisataData.forEach(w => {
        const marker = L.marker([Number(w.lat), Number(w.lng)]).addTo(map)
            .bindPopup(popupHtml(w));
        wisataMarkers[w.id] = marker;
    });

    // ─── Sesi ────────────────────────────────────────────────────────────────
    async function inisialisasiSesi(lat = null, lng = null) {
        try {
            const body = { session_token: sessionToken };
            if (lat !== null) { body.lat = lat; body.lng = lng; }

            const res = await fetch('/chat/session', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            sessionToken = data.session_token;
            localStorage.setItem('chat_session_token', sessionToken);
            return data;
        } catch (e) {
            setStatus('error', 'Gagal memulai sesi. Periksa koneksi.');
            return null;
        }
    }

    // ─── GPS ─────────────────────────────────────────────────────────────────
    function mintaLokasi() {
        if (!navigator.geolocation) {
            setStatus('error', 'Browser tidak mendukung GPS.');
            return;
        }
        setStatus('warning', 'Meminta izin lokasi...');
        navigator.geolocation.getCurrentPosition(
            async pos => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;

                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.circleMarker([userLat, userLng], {
                    radius: 9, color: '#0369a1', fillColor: '#38bdf8',
                    fillOpacity: 0.85, weight: 2,
                }).addTo(map).bindPopup('📍 Lokasi kamu').openPopup();
                map.setView([userLat, userLng], 13);

                await inisialisasiSesi(userLat, userLng);

                const sortJarak = document.getElementById('sort-jarak');
                if (sortJarak) sortJarak.disabled = false;
                if (aktifSort === 'jarak') renderList();

                document.getElementById('chip-lokasi').textContent = '📍 Lokasi Aktif';
                document.getElementById('chip-lokasi').classList.add('aktif');
                setStatus('success', 'Lokasi dikenali — rekomendasi terdekat aktif');
            },
            err => {
                setStatus('error', 'Izin lokasi ditolak. Rekomendasi tanpa jarak.');
            }
        );
    }

    // ─── Kirim pesan ─────────────────────────────────────────────────────────
    async function kirimPesan() {
        if (sedangKirim) return;
        const input = document.getElementById('input-pesan');
        const pesan = input.value.trim();
        if (!pesan) return;

        input.value = '';
        tampilPesan('user', pesan);
        sembunyikanHint();
        tampilTyping();
        setKirimDisabled(true);

        try {
            const res = await fetch('/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_token: sessionToken, pesan }),
            });

            hapusTyping();

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                tampilPesan('assistant', err.error || 'Terjadi kesalahan. Coba lagi.');
                return;
            }

            const data = await res.json();
            tampilPesan('assistant', data.jawaban, data.wisata || []);

            if (data.wisata && data.wisata.length > 0) {
                sorotWisataDiPeta(data.wisata, data.ada_lokasi);
            }

        } catch (e) {
            hapusTyping();
            tampilPesan('assistant', 'Gagal menghubungi server. Periksa koneksi.');
        } finally {
            setKirimDisabled(false);
        }
    }

    function kirimHint(btn) {
        document.getElementById('input-pesan').value = btn.textContent.trim();
        kirimPesan();
    }

    // ─── Peta: sorot hasil rekomendasi ───────────────────────────────────────
    function sorotWisataDiPeta(wisataList, adaLokasi) {
        const bounds = [];

        wisataList.forEach((w, i) => {
            const marker = wisataMarkers[w.id];
            if (marker) {
                marker.setPopupContent(popupHtml(w, i));
                if (i === 0) marker.openPopup();
                bounds.push([Number(w.lat), Number(w.lng)]);
            }
        });

        if (adaLokasi && userLat && userLng) {
            bounds.push([userLat, userLng]);
        }

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // ─── Rute ke wisata pilihan user ─────────────────────────────────────────
    let tombolRuteAktif = null;

    window.ruteKeWisata = async function(lat, lng, nama, tombolEl = null) {
        if (!userLat || !userLng) {
            setStatus('warning', 'Izinkan lokasi dulu untuk menampilkan rute.');
            mintaLokasi();
            return;
        }

        if (tombolRuteAktif && tombolRuteAktif !== tombolEl) {
            tombolRuteAktif.classList.remove('aktif');
            if (tombolRuteAktif.classList.contains('wchat-rute')) {
                tombolRuteAktif.textContent = '🗺️ Rute';
            }
        }

        if (tombolRuteAktif === tombolEl && ruteLayer) {
            map.removeLayer(ruteLayer);
            ruteLayer = null;
            if (tombolEl.classList.contains('wchat-rute')) tombolEl.textContent = '🗺️ Rute';
            tombolEl.classList.remove('aktif');
            tombolRuteAktif = null;
            setStatus('success', 'Rute dihapus');
            return;
        }

        if (tombolEl) {
            if (tombolEl.classList.contains('wchat-rute')) tombolEl.textContent = '⏳ Memuat...';
            tombolEl.classList.add('aktif');
            tombolRuteAktif = tombolEl;
        }

        map.setView([lat, lng], 14, { animate: true });

        const berhasil = await tarikRuteOSRM(userLat, userLng, lat, lng);

        if (tombolEl) {
            if (tombolEl.classList.contains('wchat-rute')) {
                tombolEl.textContent = berhasil ? '✅ Rute Aktif' : '🗺️ Rute';
            }
            if (!berhasil) tombolEl.classList.remove('aktif');
        }

        if (berhasil) {
            setStatus('success', `Menampilkan rute ke ${nama}`);
            map.fitBounds([[userLat, userLng], [lat, lng]], { padding: [50, 50] });

            if (window.innerWidth <= 1024) {
                switchMobileView('peta');
            }
        } else {
            setStatus('error', 'Rute tidak tersedia. Coba beberapa saat lagi.');
        }
    };

    // ─── OSRM rute ───────────────────────────────────────────────────────────
    async function tarikRuteOSRM(latA, lngA, latB, lngB) {
        try {
            if (ruteLayer) { map.removeLayer(ruteLayer); ruteLayer = null; }

            const url = `https://router.project-osrm.org/route/v1/driving/${lngA},${latA};${lngB},${latB}?overview=full&geometries=geojson`;
            const res  = await fetch(url);
            const data = await res.json();
            if (data.code !== 'Ok' || !data.routes[0]) return false;

            ruteLayer = L.geoJSON(data.routes[0].geometry, {
                style: { color: '#0284c7', weight: 4.5, opacity: .85, dashArray: '6 8' }
            }).addTo(map);

            return true;
        } catch (e) {
            return false;
        }
    }

    // ─── UI helpers ──────────────────────────────────────────────────────────
    function tampilPesan(role, teks, wisataList = []) {
        const container = document.getElementById('messages');
        const div = document.createElement('div');
        div.className = `msg ${role === 'user' ? 'user' : ''}`;

        const html = teks
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');

        if (role === 'user') {
            div.innerHTML = `
                <div class="avatar user-av">U</div>
                <div class="bubble user-bubble">${html}</div>`;
            container.appendChild(div);
        } else {
            let kartuHtml = '';
            if (wisataList.length > 0) {
                const kartuItems = wisataList.map((w, i) => {
                    const tiket   = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
                    const jarak   = w.jarak_km !== undefined ? ` · 📍 ${w.jarak_km} km` : '';
                    const katName = (typeof w.kategori === 'object' && w.kategori) ? w.kategori.nama : w.kategori;
                    const namaEsc = w.nama.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

                    const cuaca = w.cuaca;
                    const cuacaHtml = cuaca
                        ? `<span class="badge-cuaca ${cuaca.buruk ? 'buruk' : ''}">${cuaca.emoji} ${cuaca.label}</span>`
                        : '';

                    const status = w.status_operasional || 'normal';
                    const [stIkon, stLabel] = statusLabel(status);
                    const statusHtml = status !== 'normal'
                        ? `<span class="badge-status ${status}">${stIkon} ${stLabel}</span>`
                        : '';

                    const bermasalah = (cuaca?.buruk) || (status !== 'normal');
                    const cardClass  = bermasalah ? 'wchat-card bermasalah' : 'wchat-card';

                    return `
                        <div class="${cardClass}" onclick="fokusWisata(${w.id})">
                            <div class="wchat-num">${i + 1}</div>
                            <div class="wchat-info">
                                <div class="wchat-nama">${w.nama}</div>
                                <div class="wchat-meta">${katName} · ${tiket}${jarak}</div>
                                <div style="display:flex;gap:.25rem;flex-wrap:wrap;margin-top:.15rem">${cuacaHtml}${statusHtml}</div>
                            </div>
                            <div class="wchat-actions">
                                <button class="wchat-rute" onclick="event.stopPropagation(); ruteKeWisata(${Number(w.lat)}, ${Number(w.lng)}, '${namaEsc}', this)">
                                    🗺️ Rute
                                </button>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${Number(w.lat)},${Number(w.lng)}" target="_blank" rel="noopener" class="wchat-gmaps" onclick="event.stopPropagation();" title="Navigasi via Google Maps">
                                    🚗 G-Maps
                                </a>
                            </div>
                        </div>`;
                }).join('');
                kartuHtml = `<div class="wisata-cards">${kartuItems}</div>`;
            }

            div.innerHTML = `
                <div class="avatar">AI</div>
                <div class="bubble reveal">${html}${kartuHtml}</div>`;
            container.appendChild(div);
        }
        container.scrollTop = container.scrollHeight;
    }

    function tampilTyping() {
        const container = document.getElementById('messages');
        const div = document.createElement('div');
        div.className = 'msg';
        div.id = 'typing-indicator';
        div.innerHTML = `<div class="avatar thinking">AI</div><div class="bubble"><div class="typing"><span></span><span></span><span></span></div></div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function hapusTyping() {
        document.getElementById('typing-indicator')?.remove();
    }

    function sembunyikanHint() {
        const hint = document.getElementById('hint');
        if (!hint || hint.style.display === 'none') return;
        hint.classList.add('fade-out');
        setTimeout(() => { hint.style.display = 'none'; }, 200);
    }

    function setKirimDisabled(disabled) {
        sedangKirim = disabled;
        document.getElementById('send-btn').disabled = disabled;
        document.getElementById('input-pesan').disabled = disabled;
        if (!disabled) document.getElementById('input-pesan').focus();
    }

    let statusTimer = null;
    function setStatus(tipe, teks) {
        const bar  = document.getElementById('status-bar');
        const span = document.getElementById('status-text');
        bar.className = 'status-bar';
        bar.style.opacity = '1';
        if (tipe === 'success') bar.classList.add('success');
        else if (tipe === 'error') bar.classList.add('error-bar');
        else bar.classList.add('warning');
        span.textContent = teks;

        clearTimeout(statusTimer);
        if (tipe === 'success') {
            statusTimer = setTimeout(() => { bar.style.opacity = '0'; }, 4000);
        }
    }

    // ─── Enter key ───────────────────────────────────────────────────────────
    document.getElementById('input-pesan').addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); kirimPesan(); }
    });

    // ─── Boot ────────────────────────────────────────────────────────────────
    (async () => {
        if (window.innerWidth <= 1024) {
            document.body.classList.add('mobile-view-peta');
        }
        const sesi = await inisialisasiSesi();
        if (sesi) {
            setStatus('success', 'Chatbot siap — ketik pertanyaan atau izinkan lokasi');
            if (sesi.lat && sesi.lng) {
                userLat = parseFloat(sesi.lat);
                userLng = parseFloat(sesi.lng);
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.circleMarker([userLat, userLng], {
                    radius: 9, color: '#0369a1', fillColor: '#38bdf8',
                    fillOpacity: 0.85, weight: 2,
                }).addTo(map).bindPopup('📍 Lokasi kamu');
                document.getElementById('chip-lokasi').textContent = '📍 Lokasi Aktif';
                document.getElementById('chip-lokasi').classList.add('aktif');
            }
        }
    })();
    </script>
</body>
</html>

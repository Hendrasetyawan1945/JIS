<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        :root {
            --teal: #0d9488;
            --teal-dark: #0f766e;
            --ocean: #0369a1;
            --sand: #f59e0b;
            --ink: #0f172a;
            --muted: #64748b;
            --bg: #f1f5f9;
            --card: #ffffff;
            --line: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--bg); color: var(--ink); min-height: 100vh; }

        .hero {
            background: linear-gradient(120deg, var(--teal) 0%, var(--ocean) 100%);
            color: #fff;
            padding: 2rem 2rem 3.5rem;
        }
        .hero-inner { max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
        .brand { display: flex; gap: 0.9rem; align-items: center; }
        .logo { width: 52px; height: 52px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.35); border-radius: 14px; display: grid; place-items: center; flex: none; }
        .hero h1 { font-size: 1.45rem; font-weight: 800; letter-spacing: -.02em; }
        .hero p { opacity: .85; font-size: .9rem; margin-top: .2rem; }
        .stats { display: flex; gap: .6rem; flex-wrap: wrap; }
        .chip { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(4px); padding: .45rem .9rem; border-radius: 999px; font-size: .8rem; font-weight: 600; }
        .chip-lokasi { cursor: pointer; transition: background .2s; }
        .chip-lokasi:hover { background: rgba(255,255,255,.25); }
        .chip-lokasi.aktif { background: rgba(16,185,129,.35); border-color: rgba(16,185,129,.6); }

        .layout { max-width: 1280px; margin: -2.5rem auto 2rem; padding: 0 2rem; display: flex; gap: 1.25rem; align-items: stretch; }
        .card { background: var(--card); border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 10px 30px -12px rgba(15,23,42,.18); overflow: hidden; }

        /* Sidebar list wisata */
        .list-card { width: 320px; flex: none; display: flex; flex-direction: column; }
        .card-head .sort-toggle { margin-left: auto; background: none; border: 1px solid var(--line); border-radius: 6px; padding: .15rem .4rem; cursor: pointer; font-size: .9rem; color: var(--muted); transition: background .15s; }
        .card-head .sort-toggle:hover { background: var(--bg); color: var(--teal-dark); }
        .search-row { display: flex; align-items: center; gap: .4rem; padding: .55rem .8rem; border-bottom: 1px solid var(--line); }
        .search-icon { color: var(--muted); font-size: .9rem; }
        .search-row input { flex: 1; border: none; background: transparent; font: inherit; font-size: .85rem; color: var(--ink); outline: none; }
        .search-row input::placeholder { color: var(--muted); }
        .sort-menu { display: none; flex-direction: column; padding: .35rem .5rem; background: #f8fafc; border-bottom: 1px solid var(--line); }
        .sort-menu.show { display: flex; }
        .sort-opt { text-align: left; background: none; border: none; padding: .4rem .55rem; border-radius: 6px; font: inherit; font-size: .8rem; color: var(--ink); cursor: pointer; }
        .sort-opt:hover { background: var(--bg); }
        .sort-opt.active { background: var(--teal); color: #fff; font-weight: 600; }
        .sort-opt:disabled { color: #94a3b8; cursor: not-allowed; }
        .sort-opt:disabled:hover { background: none; }
        .filter-chips { display: flex; gap: .35rem; flex-wrap: wrap; padding: .7rem 1rem; border-bottom: 1px solid var(--line); }
        .filter-chips .chip { cursor: pointer; padding: .35rem .8rem; border-radius: 999px; font-size: .75rem; font-weight: 600; background: #f1f5f9; color: #475569; border: 1px solid transparent; transition: background .15s, color .15s; }
        .filter-chips .chip:hover { background: #e2e8f0; }
        .filter-chips .chip.active { background: var(--teal); color: #fff; border-color: var(--teal-dark); }
        .list-body { flex: 1; overflow-y: auto; padding: .6rem; display: flex; flex-direction: column; gap: .6rem; max-height: 65vh; }
        .list-body::-webkit-scrollbar { width: 5px; }
        .list-body::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
        .empty-state { display: flex; flex-direction: column; align-items: center; gap: .5rem; padding: 2rem 1rem; color: var(--muted); font-size: .85rem; text-align: center; }
        .empty-state .icon { font-size: 2.5rem; opacity: .5; }
        .list-meta { padding: .35rem .55rem 0; font-size: .7rem; color: var(--muted); font-weight: 600; }
        .wcard { display: flex; gap: .65rem; padding: .55rem; border-radius: 12px; border: 1px solid var(--line); background: #fff; cursor: pointer; transition: border-color .15s, background .15s; flex-wrap: wrap; }
        .wcard:hover { border-color: var(--teal); background: #f0fdfa; }
        .wcard.sorot { border-color: var(--teal); background: #ccfbf1; }
        .wcard-foto { width: 64px; height: 64px; flex: none; border-radius: 8px; background: #e2e8f0; object-fit: cover; display: block; }
        .wcard-foto.fallback { display: grid; place-items: center; color: var(--muted); font-size: .65rem; font-weight: 700; }
        .wcard-foto-wrap { width: 64px; height: 64px; flex: none; border-radius: 8px; overflow: hidden; }
        .wcard-foto-wrap.skeleton { border-radius: 8px; }
        .wcard-body { flex: 1; min-width: 0; }
        .wcard-nama { font-weight: 700; font-size: .85rem; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .wcard-kat { display: inline-block; margin-top: .15rem; font-size: .65rem; font-weight: 600; color: var(--teal-dark); background: #ccfbf1; padding: .1rem .5rem; border-radius: 999px; }
        .wcard-meta { margin-top: .25rem; font-size: .72rem; color: var(--muted); display: flex; gap: .6rem; flex-wrap: wrap; }
        .wcard-rute { width: 100%; margin-top: .35rem; padding: .3rem .7rem; border-radius: 8px; background: var(--teal); color: #fff; border: none; font: inherit; font-size: .75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .3rem; transition: background .15s, transform .15s, box-shadow .15s; }
        .wcard-rute:hover { background: var(--teal-dark); transform: translateY(-1px); box-shadow: 0 4px 12px -2px rgba(13,148,136,.35); }
        .wcard-rute:active { transform: scale(.96); }
        .wcard-rute.aktif { background: var(--ocean); animation: routePulse 2s ease-in-out infinite; }
        /* Skeleton loader untuk foto */
        .skeleton { background: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 50%, #e2e8f0 100%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .map-card { flex: 1; min-width: 320px; display: flex; flex-direction: column; }
        .card-head { padding: .8rem 1.1rem; font-size: .8rem; font-weight: 600; color: var(--muted); border-bottom: 1px solid var(--line); display: flex; align-items: center; gap: .45rem; }
        .card-head svg { flex: none; }
        #map { flex: 1; height: 65vh; min-height: 420px; }

        .chat-card { width: 370px; flex: none; display: flex; flex-direction: column; }
        .chat-body { padding: 1.1rem; display: flex; flex-direction: column; flex: 1; gap: .9rem; min-height: 0; }

        .messages { flex: 1; display: flex; flex-direction: column; gap: .7rem; overflow-y: auto; max-height: calc(65vh - 60px); padding-right: .2rem; }
        .messages::-webkit-scrollbar { width: 4px; }
        .messages::-webkit-scrollbar-track { background: transparent; }
        .messages::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }

        .msg { display: flex; gap: .55rem; animation: msgIn .35s cubic-bezier(.34,1.56,.64,1) both; }
        .msg.user { flex-direction: row-reverse; animation-name: msgInRight; }
        @keyframes msgIn {
            from { opacity: 0; transform: translateX(-14px) translateY(6px); }
            to   { opacity: 1; transform: translateX(0) translateY(0); }
        }
        @keyframes msgInRight {
            from { opacity: 0; transform: translateX(14px) translateY(6px); }
            to   { opacity: 1; transform: translateX(0) translateY(0); }
        }

        .avatar { width: 32px; height: 32px; border-radius: 10px; background: linear-gradient(135deg, var(--teal), var(--ocean)); color: #fff; display: grid; place-items: center; font-size: .7rem; font-weight: 800; flex: none; transition: transform .2s; }
        .avatar.user-av { background: linear-gradient(135deg, var(--sand), #ef4444); }
        .avatar.user-av.pop { animation: avatarPop .35s cubic-bezier(.34,1.56,.64,1); }
        @keyframes avatarPop {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.3) rotate(-8deg); }
            100% { transform: scale(1) rotate(0); }
        }
        /* Avatar AI berputar gradiennya saat typing */
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

        .bubble { background: #f8fafc; border: 1px solid var(--line); padding: .65rem .9rem; border-radius: 4px 14px 14px 14px; font-size: .86rem; line-height: 1.55; color: #334155; max-width: 88%; }
        .bubble.user-bubble { background: var(--teal); border-color: var(--teal-dark); color: #fff; border-radius: 14px 4px 14px 14px; }
        /* Bubble AI muncul dengan reveal dari kiri */
        .bubble.reveal { animation: bubbleReveal .3s ease both; }
        @keyframes bubbleReveal {
            from { opacity: 0; transform: scale(.96) translateY(4px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .bubble strong { color: var(--teal-dark); }
        .bubble.user-bubble strong { color: #ccfbf1; }
        .bubble p { margin: .3rem 0; }
        .bubble p:first-child { margin-top: 0; }
        .bubble p:last-child { margin-bottom: 0; }

        /* loading dots — lebih hidup */
        .typing { display: flex; gap: 5px; align-items: center; padding: .5rem .7rem; }
        .typing span { width: 8px; height: 8px; border-radius: 50%; background: var(--teal); animation: typingDot 1.1s ease-in-out infinite; }
        .typing span:nth-child(2) { animation-delay: .2s; background: var(--ocean); }
        .typing span:nth-child(3) { animation-delay: .4s; background: var(--sand); }
        @keyframes typingDot {
            0%, 60%, 100% { transform: translateY(0) scale(1); opacity: .5; }
            30%            { transform: translateY(-6px) scale(1.2); opacity: 1; }
        }

        .hint { display: grid; gap: .45rem; }
        .hint button { text-align: left; font: inherit; font-size: .82rem; padding: .5rem .8rem; border-radius: 10px; border: 1.5px solid var(--line); background: #f8fafc; color: var(--teal-dark); cursor: pointer; transition: background .15s, border-color .15s, opacity .3s; }
        .hint button:hover { background: #f0fdfa; border-color: var(--teal); }
        .hint button:disabled { cursor: not-allowed; opacity: .5; }
        .hint.fade-out { opacity: 0; pointer-events: none; }

        .input { display: flex; gap: .5rem; }
        .input input { flex: 1; padding: .7rem .9rem; border-radius: 12px; border: 1px solid var(--line); background: #f8fafc; font: inherit; font-size: .88rem; color: var(--ink); outline: none; transition: border-color .15s; }
        .input input:focus { border-color: var(--teal); }
        .input input:disabled { cursor: not-allowed; color: var(--muted); background: #f1f5f9; }
        .send-btn { padding: .7rem 1.1rem; border-radius: 12px; border: none; background: var(--teal); color: #fff; font: inherit; font-weight: 600; cursor: pointer; transition: background .15s, transform .15s, box-shadow .15s; }
        .send-btn:hover:not(:disabled) { background: var(--teal-dark); transform: translateY(-1px); box-shadow: 0 4px 12px -2px rgba(13,148,136,.4); }
        .send-btn:active:not(:disabled) { transform: scale(.95); }
        .send-btn:disabled { opacity: .5; cursor: not-allowed; }

        .status-bar { display: flex; align-items: center; gap: .4rem; font-size: .8rem; font-weight: 600; padding: .45rem .7rem; border-radius: 10px; transition: opacity .4s; }
        .status-bar.warning { color: var(--sand); background: #fffbeb; border: 1px solid #fde68a; }
        .status-bar.success { color: #059669; background: #ecfdf5; border: 1px solid #6ee7b7; }
        .status-bar.error-bar { color: #dc2626; background: #fef2f2; border: 1px solid #fca5a5; }

        /* Kartu wisata di dalam chat bubble */
        .wisata-cards { display: flex; flex-direction: column; gap: .45rem; margin-top: .6rem; max-height: 260px; overflow-y: auto; padding-right: .15rem; }
        .wisata-cards::-webkit-scrollbar { width: 4px; }
        .wisata-cards::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
        .wchat-card { display: flex; gap: .55rem; align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: .5rem .6rem; cursor: pointer; transition: border-color .15s, background .15s, transform .15s, box-shadow .15s;
            animation: cardStagger .35s cubic-bezier(.34,1.4,.64,1) both; }
        /* Stagger per kartu 0–4 */
        .wchat-card:nth-child(1) { animation-delay: .05s; }
        .wchat-card:nth-child(2) { animation-delay: .12s; }
        .wchat-card:nth-child(3) { animation-delay: .19s; }
        .wchat-card:nth-child(4) { animation-delay: .26s; }
        .wchat-card:nth-child(5) { animation-delay: .33s; }
        @keyframes cardStagger {
            from { opacity: 0; transform: translateX(-10px) scale(.97); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        .wchat-card:hover { border-color: var(--teal); background: #f0fdfa; transform: translateX(3px); box-shadow: 0 2px 8px -2px rgba(13,148,136,.2); }
        .wchat-num { min-width: 20px; height: 20px; border-radius: 50%; background: var(--teal); color: #fff; font-size: .68rem; font-weight: 800; display: grid; place-items: center; flex: none; }
        .wchat-info { flex: 1; min-width: 0; }
        .wchat-nama { font-weight: 700; font-size: .82rem; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .wchat-meta { font-size: .72rem; color: var(--muted); margin-top: .1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .wchat-actions { display: flex; flex-direction: column; gap: .3rem; align-items: flex-end; flex: none; }
        .wchat-rute { flex: none; background: var(--teal); color: #fff; border: none; border-radius: 8px; padding: .3rem .6rem; font: inherit; font-size: .72rem; font-weight: 700; cursor: pointer; white-space: nowrap; min-width: fit-content; transition: background .15s, transform .15s, box-shadow .15s; display: flex; align-items: center; gap: .25rem; }
        .wchat-rute:hover { background: var(--teal-dark); transform: scale(1.06); box-shadow: 0 3px 10px -2px rgba(13,148,136,.4); }
        .wchat-rute:active { transform: scale(.95); }
        .wchat-rute.aktif { background: var(--ocean); animation: routePulse 2s ease-in-out infinite; }
        .wchat-gmaps { flex: none; background: #0284c7; color: #fff; border: none; border-radius: 8px; padding: .25rem .55rem; font: inherit; font-size: .68rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; transition: background .15s, transform .15s; display: inline-flex; align-items: center; gap: .2rem; }
        .wchat-gmaps:hover { background: #0369a1; transform: scale(1.05); }
        @keyframes routePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(3,105,161,.4); }
            50%       { box-shadow: 0 0 0 6px rgba(3,105,161,0); }
        }

        /* Popup detail lengkap */
        .pop-foto { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: .55rem; background: #e2e8f0; }
        .pop-foto.fallback { display: grid; place-items: center; color: var(--muted); font-size: .8rem; font-weight: 700; height: 80px; }
        .leaflet-popup-content { width: 270px !important; }
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 8px 24px -8px rgba(15,23,42,.25); }
        .leaflet-popup-content { font-family: 'Plus Jakarta Sans', sans-serif; margin: .8rem; line-height: 1.5; }
        .pop-nama { font-weight: 800; font-size: .95rem; }
        .pop-kat { display: inline-block; margin-top: .2rem; font-size: .7rem; font-weight: 600; color: var(--teal-dark); background: #ccfbf1; padding: .15rem .55rem; border-radius: 999px; }
        .pop-row { margin-top: .35rem; font-size: .76rem; color: #475569; }
        .pop-desc { font-size: .74rem; color: #334155; margin-top: .5rem; line-height: 1.45; max-height: 72px; overflow-y: auto; }
        .pop-desc::-webkit-scrollbar { width: 3px; }
        .pop-desc::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }
        .pop-jarak { color: var(--ocean); font-weight: 600; }
        .pop-actions { display: flex; gap: .4rem; margin-top: .6rem; flex-wrap: wrap; }
        .pop-actions a { font-size: .72rem; padding: .3rem .6rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: .25rem; }
        .pop-call { background: var(--teal); color: #fff; }
        .pop-gmaps { background: #0284c7; color: #fff; }
        .pop-gmaps:hover { background: #0369a1; }
        .pop-map { background: #e2e8f0; color: #334155; }

        /* Badge cuaca & status — dipakai di popup, kartu chat, kartu sidebar */
        .badge-cuaca { display: inline-flex; align-items: center; gap: .25rem; font-size: .7rem; font-weight: 600; padding: .18rem .55rem; border-radius: 999px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; margin-top: .3rem; }
        .badge-cuaca.buruk { background: #fff7ed; color: #c2410c; border-color: #fed7aa; animation: badgePulse 2s ease-in-out infinite; }
        .badge-status { display: inline-flex; align-items: center; gap: .25rem; font-size: .7rem; font-weight: 700; padding: .18rem .55rem; border-radius: 999px; margin-top: .25rem; }
        .badge-status.normal        { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-status.tutup_sementara { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-status.renovasi      { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-status.banjir        { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-status.longsor       { background: #fdf4ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-status.akses_terbatas { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        @keyframes badgePulse {
            0%,100% { opacity: 1; }
            50%      { opacity: .65; }
        }
        /* Kartu chat bermasalah — border merah tipis */
        .wchat-card.bermasalah { border-color: #fca5a5; background: #fff5f5; }
        .wchat-card.bermasalah:hover { border-color: #ef4444; background: #fef2f2; }

        footer { text-align: center; padding: 1.2rem; font-size: .75rem; color: var(--muted); }

        /* Floating chat button (mobile) */
        .fab-chat { display: none; position: fixed; bottom: 1.2rem; right: 1.2rem; z-index: 20; width: 56px; height: 56px; border-radius: 50%; background: var(--teal); color: #fff; border: none; box-shadow: 0 8px 24px -4px rgba(13,148,136,.45); cursor: pointer; font-size: 1.4rem; transition: transform .15s, background .15s; }
        .fab-chat:hover { background: var(--teal-dark); transform: translateY(-2px); }
        @media (max-width: 900px) {
            .fab-chat { display: grid; place-items: center; }
        }

        @media (max-width: 900px) {
            .layout { flex-direction: column; padding: 0 1rem; margin-top: -2rem; }
            .chat-card { width: auto; flex: 1 1 auto; }
            .list-card { width: auto; flex: 1 1 auto; max-height: 40vh; }
            .list-body { max-height: 30vh; }
            #map { height: 45vh; min-height: 320px; }
            .hero { padding: 1.4rem 1.2rem 3rem; }
            .hero h1 { font-size: 1.2rem; }
            .messages { max-height: 38vh; }
            .wisata-cards { max-height: 200px; }
        }
    </style>
</head>
<body>
    <header class="hero">
        <div class="hero-inner">
            <div class="brand">
                <div class="logo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                    <h1>{{ config('app.name') }}</h1>
                    <p>Chatbot AI rekomendasi wisata berbasis data SQL</p>
                </div>
            </div>
            <div class="stats">
                <span class="chip">{{ $wisata->count() }} Tempat Wisata</span>
                <span class="chip">{{ $wisata->pluck('kategori.nama')->unique()->count() }} Kategori</span>
                <span class="chip" title="Rating rata-rata semua wisata">⭐ {{ number_format((float) $wisata->avg('rating'), 1) }}</span>
                <span class="chip chip-lokasi" id="chip-lokasi" onclick="mintaLokasi()">
                    📍 Izinkan Lokasi
                </span>
            </div>
        </div>
    </header>

    <main class="layout">
        <aside class="card list-card" id="list-card">
            <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Daftar Wisata
                <button class="sort-toggle" id="sort-toggle" title="Urutkan" aria-label="Urutkan">⇅</button>
            </div>
            <div class="search-row">
                <span class="search-icon">🔍</span>
                <input type="search" id="search-input" placeholder="Cari nama tempat..." autocomplete="off">
            </div>
            <div class="sort-menu" id="sort-menu">
                <button class="sort-opt active" data-sort="rating">⭐ Rating tertinggi</button>
                <button class="sort-opt" data-sort="name-asc">🔤 Nama A–Z</button>
                <button class="sort-opt" data-sort="name-desc">🔤 Nama Z–A</button>
                <button class="sort-opt" data-sort="price-asc">💰 Termurah dulu</button>
                <button class="sort-opt" data-sort="price-desc">💎 Termahal dulu</button>
                <button class="sort-opt" data-sort="jarak" id="sort-jarak" disabled>📍 Jarak (izin lokasi dulu)</button>
            </div>
            <div class="filter-chips" id="filter-chips">
                <button class="chip active" data-kat="*">Semua</button>
                <button class="chip" data-kat="Pantai">Pantai</button>
                <button class="chip" data-kat="Pulau">Pulau</button>
                <button class="chip" data-kat="Alam">Alam</button>
                <button class="chip" data-kat="Museum">Museum</button>
                <button class="chip" data-kat="Sejarah">Sejarah</button>
                <button class="chip" data-kat="Kuliner">Kuliner</button>
            </div>
            <div class="list-body" id="list-body">
                {{-- Diisi via JS dari wisataData --}}
            </div>
        </aside>

        <section class="card map-card">
            <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Peta Interaktif — klik marker atau kartu untuk detail
            </div>
            <div id="map"></div>
        </section>

        <aside class="card chat-card">
            <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Chat Rekomendasi
            </div>
            <div class="chat-body">
                <div class="messages" id="messages">
                    <div class="msg">
                        <div class="avatar">AI</div>
                        <div class="bubble">Halo! Saya siap bantu cari wisata di Kota Padang. <br>Contoh pertanyaan:</div>
                    </div>
                </div>

                <div class="hint" id="hint">
                    <button onclick="kirimHint(this)">Pantai terdekat dari lokasi saya</button>
                    <button onclick="kirimHint(this)">Wisata yang buka sekarang</button>
                    <button onclick="kirimHint(this)">Tempat kuliner khas Padang yang wajib dicoba</button>
                    <button onclick="kirimHint(this)">Museum yang cocok untuk edukasi keluarga</button>
                </div>

                <div class="input">
                    <input type="text" id="input-pesan" placeholder="Ketik pesan..." autocomplete="off">
                    <button class="send-btn" id="send-btn" onclick="kirimPesan()">Kirim</button>
                </div>

                <div class="status-bar warning" id="status-bar">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span id="status-text">Memulai sesi...</span>
                </div>
            </div>
        </aside>
    </main>

    <footer>{{ config('app.name') }} &middot; Fase 1 &middot; Data &amp; peta: OpenStreetMap &middot; AI: DeepSeek</footer>

    <button class="fab-chat" id="fab-chat" onclick="scrollKeChat()" aria-label="Buka chat" title="Buka chat rekomendasi">💬</button>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    // ─── State ───────────────────────────────────────────────────────────────
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    let sessionToken = localStorage.getItem('chat_session_token') || null;
    let userLat = null, userLng = null;
    let userMarker = null;
    let ruteLayer  = null;
    let sedangKirim = false;

    // ─── Peta ────────────────────────────────────────────────────────────────
    const map = L.map('map').setView([-0.9471, 100.4174], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker semua wisata dari server
    const wisataData = @json($wisata);
    const wisataMarkers = {};

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
        // Filter kategori
        if (aktifFilter !== '*') {
            list = list.filter(w => w.kategori.nama === aktifFilter);
        }
        // Search
        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            list = list.filter(w =>
                w.nama.toLowerCase().includes(q) ||
                (w.alamat || '').toLowerCase().includes(q) ||
                (w.deskripsi || '').toLowerCase().includes(q) ||
                w.kategori.nama.toLowerCase().includes(q)
            );
        }
        // Sort
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
        const meta = `<div class="list-meta">${list.length} tempat</div>`;
        if (list.length === 0) {
            listBody.innerHTML = meta + `
                <div class="empty-state">
                    <div class="icon">🗺️</div>
                    <div>Tidak ada wisata yang cocok dengan filter "${searchQuery || aktifFilter}".</div>
                    <button class="sort-opt" onclick="document.getElementById('search-input').value=''; window.dispatchEvent(new Event('clear-search'));">Reset pencarian</button>
                </div>`;
            return;
        }
        listBody.innerHTML = meta + list.map(w => {
            const fotoHtml = w.foto
                ? `<div class="wcard-foto-wrap skeleton"><img class="wcard-foto" src="${w.foto}" alt="${w.nama}" loading="lazy" onload="this.parentElement.classList.remove('skeleton')" onerror="this.outerHTML='<div class=\\'wcard-foto fallback\\'>${w.kategori.nama[0]}</div>'"></div>`
                : `<div class="wcard-foto fallback">${w.kategori.nama[0]}</div>`;
            const tiket = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
            const jarak = w._jarak !== undefined ? ` · ${w._jarak.toFixed(1)}km` : '';
            const status = w.status_operasional || 'normal';
            const [stIkon, stLabel] = statusLabel(status);
            const statusBadge = status !== 'normal'
                ? `<span class="badge-status ${status}" style="font-size:.62rem;padding:.1rem .4rem">${stIkon} ${stLabel}</span>`
                : '';
            return `
                <div class="wcard" data-id="${w.id}" onclick="fokusWisata(${w.id})">
                    ${fotoHtml}
                    <div class="wcard-body">
                        <div class="wcard-nama">${w.nama}</div>
                        <div style="display:flex;gap:.3rem;align-items:center;flex-wrap:wrap;margin-top:.15rem">
                            <span class="wcard-kat">${w.kategori.nama}</span>${statusBadge}
                        </div>
                        <div class="wcard-meta">
                            <span>${tiket}</span>
                            <span>⭐ ${Number(w.rating).toFixed(1)}${jarak}</span>
                        </div>
                    </div>
                    <button class="wcard-rute" onclick="event.stopPropagation(); ruteKeWisata(${Number(w.lat)}, ${Number(w.lng)}, '${w.nama.replace(/'/g,"\\'")}', this)">🗺️ Rute</button>
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
        const katName = (typeof w.kategori === 'object' && w.kategori) ? w.kategori.nama : w.kategori;
        const fotoHtml = w.foto
            ? `<img class="pop-foto" src="${w.foto}" alt="${w.nama}" loading="lazy" onerror="this.outerHTML='<div class=\\'pop-foto fallback\\'>📷 Tidak ada foto</div>'">`
            : '<div class="pop-foto fallback">📷 Tidak ada foto</div>';
        const tiket = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
        const telp  = w.telepon ? `<a href="tel:${w.telepon}" class="pop-call">📞 ${w.telepon}</a>` : '';
        const gmaps = `<a href="https://www.google.com/maps/dir/?api=1&destination=${w.lat},${w.lng}" target="_blank" rel="noopener" class="pop-gmaps" title="Navigasi langsung via Google Maps">🚗 G-Maps</a>`;
        const osm   = `<a href="https://www.openstreetmap.org/?mlat=${w.lat}&mlon=${w.lng}#map=17/${w.lat}/${w.lng}" target="_blank" rel="noopener" class="pop-map">🗺️ OSM</a>`;

        // Badge cuaca
        const cuaca = w.cuaca;
        const cuacaHtml = cuaca
            ? `<div><span class="badge-cuaca ${cuaca.buruk ? 'buruk' : ''}">${cuaca.emoji} ${cuaca.label} · ${cuaca.suhu}°C</span></div>`
            : '';

        // Badge status operasional
        const status = w.status_operasional || 'normal';
        const [stIkon, stLabel] = statusLabel(status);
        const statusHtml = `<div><span class="badge-status ${status}">${stIkon} ${stLabel}</span>${w.catatan_status ? `<span style="font-size:.68rem;color:#64748b;margin-left:.4rem">${w.catatan_status}</span>` : ''}</div>`;

        return `
            ${fotoHtml}
            <div class="pop-nama">${nomor}${w.nama}</div>
            <span class="pop-kat">${katName}</span>
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

    // Fungsi global agar onclick di HTML bisa panggil
    window.fokusWisata = function(id) {
        const marker = wisataMarkers[id];
        if (!marker) return;
        map.setView(marker.getLatLng(), 14, { animate: true });
        marker.openPopup();
        // Sorot kartu di sidebar
        document.querySelectorAll('.wcard').forEach(el => el.classList.remove('sorot'));
        const card = document.querySelector(`.wcard[data-id="${id}"]`);
        if (card) {
            card.classList.add('sorot');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // Floating chat button — scroll ke chat-card & fokus input
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

    sortToggle.addEventListener('click', () => {
        sortMenu.classList.toggle('show');
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
    // Reset dari empty-state button
    window.addEventListener('clear-search', () => {
        searchQuery = '';
        searchInput.value = '';
        renderList();
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

                // Tampilkan marker user di peta
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.circleMarker([userLat, userLng], {
                    radius: 10, color: '#0369a1', fillColor: '#38bdf8',
                    fillOpacity: 0.85, weight: 2,
                }).addTo(map).bindPopup('📍 Lokasi kamu').openPopup();
                map.setView([userLat, userLng], 13);

                // Update sesi dengan lokasi baru
                await inisialisasiSesi(userLat, userLng);

                // Aktifkan sort berdasarkan jarak
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
            // Kirim data wisata ke tampilPesan agar bisa render kartu + tombol rute
            tampilPesan('assistant', data.jawaban, data.wisata || []);

            // Sorot marker di peta (tanpa auto-rute)
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

    // ─── Peta: sorot hasil rekomendasi (tanpa auto-rute) ─────────────────────
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
    // tombolEl opsional — untuk update state visual tombol yang diklik
    let tombolRuteAktif = null; // referensi tombol yang sedang aktif

    window.ruteKeWisata = async function(lat, lng, nama, tombolEl = null) {
        // Kalau belum ada lokasi user, minta izin dulu
        if (!userLat || !userLng) {
            setStatus('warning', 'Izinkan lokasi dulu untuk menampilkan rute.');
            mintaLokasi();
            return;
        }

        // Reset tombol aktif sebelumnya
        if (tombolRuteAktif && tombolRuteAktif !== tombolEl) {
            tombolRuteAktif.textContent = '🗺️ Rute';
            tombolRuteAktif.classList.remove('aktif');
        }

        // Toggle — klik tombol yang sama lagi = hapus rute
        if (tombolRuteAktif === tombolEl && ruteLayer) {
            map.removeLayer(ruteLayer);
            ruteLayer = null;
            tombolEl.textContent = '🗺️ Rute';
            tombolEl.classList.remove('aktif');
            tombolRuteAktif = null;
            setStatus('success', 'Rute dihapus');
            return;
        }

        // Update UI tombol
        if (tombolEl) {
            tombolEl.textContent = '⏳ Memuat...';
            tombolEl.classList.add('aktif');
            tombolRuteAktif = tombolEl;
        }

        // Fokus peta ke wisata tujuan
        map.setView([lat, lng], 14, { animate: true });

        // Tarik rute OSRM
        const berhasil = await tarikRuteOSRM(userLat, userLng, lat, lng);

        if (tombolEl) {
            tombolEl.textContent = berhasil ? '✅ Rute Aktif' : '🗺️ Rute';
            if (!berhasil) tombolEl.classList.remove('aktif');
        }

        if (berhasil) {
            setStatus('success', `Menampilkan rute ke ${nama}`);
            // Fit bounds: lokasi user + tujuan
            map.fitBounds([[userLat, userLng], [lat, lng]], { padding: [60, 60] });

            // Pengalaman mobile: otomatis scroll halus ke peta agar rute langsung terlihat
            if (window.innerWidth <= 900) {
                const mapCard = document.querySelector('.map-card');
                if (mapCard) {
                    mapCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        } else {
            setStatus('error', 'Rute tidak tersedia. Coba beberapa saat lagi.');
        }
    };

    // ─── OSRM rute ───────────────────────────────────────────────────────────
    async function tarikRuteOSRM(latA, lngA, latB, lngB) {
        try {
            // Hapus rute lama sebelum gambar yang baru
            if (ruteLayer) { map.removeLayer(ruteLayer); ruteLayer = null; }

            const url = `https://router.project-osrm.org/route/v1/driving/${lngA},${latA};${lngB},${latB}?overview=full&geometries=geojson`;
            const res  = await fetch(url);
            const data = await res.json();
            if (data.code !== 'Ok' || !data.routes[0]) return false;

            ruteLayer = L.geoJSON(data.routes[0].geometry, {
                style: { color: '#0369a1', weight: 4, opacity: .8, dashArray: '8 6' }
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

        // Konversi **bold** dan newline ke HTML
        const html = teks
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');

        if (role === 'user') {
            div.innerHTML = `
                <div class="avatar user-av">U</div>
                <div class="bubble user-bubble">${html}</div>`;
            container.appendChild(div);
            // Animasi pop pada avatar user
            const av = div.querySelector('.avatar.user-av');
            requestAnimationFrame(() => av.classList.add('pop'));
        } else {
            // Render kartu wisata interaktif jika ada data
            let kartuHtml = '';
            if (wisataList.length > 0) {
                const kartuItems = wisataList.map((w, i) => {
                    const tiket   = Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID');
                    const jarak   = w.jarak_km !== undefined ? ` · 📍 ${w.jarak_km} km` : '';
                    const katName = (typeof w.kategori === 'object' && w.kategori) ? w.kategori.nama : w.kategori;
                    const namaEsc = w.nama.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

                    // Badge cuaca
                    const cuaca = w.cuaca;
                    const cuacaHtml = cuaca
                        ? `<span class="badge-cuaca ${cuaca.buruk ? 'buruk' : ''}">${cuaca.emoji} ${cuaca.label}</span>`
                        : '';

                    // Badge status
                    const status = w.status_operasional || 'normal';
                    const [stIkon, stLabel] = statusLabel(status);
                    const statusHtml = status !== 'normal'
                        ? `<span class="badge-status ${status}">${stIkon} ${stLabel}</span>`
                        : '';

                    // Kartu bermasalah jika cuaca buruk atau status tidak normal
                    const bermasalah = (cuaca?.buruk) || (status !== 'normal');
                    const cardClass  = bermasalah ? 'wchat-card bermasalah' : 'wchat-card';

                    return `
                        <div class="${cardClass}" onclick="fokusWisata(${w.id})">
                            <div class="wchat-num">${i + 1}</div>
                            <div class="wchat-info">
                                <div class="wchat-nama">${w.nama}</div>
                                <div class="wchat-meta">${katName} · ${tiket}${jarak}</div>
                                <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-top:.2rem">${cuacaHtml}${statusHtml}</div>
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
        // Avatar AI beranimasi saat typing
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
        setTimeout(() => { hint.style.display = 'none'; }, 300);
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

        // Auto-hide setelah 4 detik untuk pesan sukses
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
        const sesi = await inisialisasiSesi();
        if (sesi) {
            setStatus('success', 'Chatbot siap — ketik pertanyaan atau izinkan lokasi');
            // Jika sesi lama sudah punya lokasi tersimpan, pakai
            if (sesi.lat && sesi.lng) {
                userLat = parseFloat(sesi.lat);
                userLng = parseFloat(sesi.lng);
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.circleMarker([userLat, userLng], {
                    radius: 10, color: '#0369a1', fillColor: '#38bdf8',
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

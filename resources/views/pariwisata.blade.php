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

        .map-card { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .card-head { padding: .8rem 1.1rem; font-size: .8rem; font-weight: 600; color: var(--muted); border-bottom: 1px solid var(--line); display: flex; align-items: center; gap: .45rem; }
        .card-head svg { flex: none; }
        #map { flex: 1; height: 65vh; min-height: 420px; }

        .chat-card { width: 370px; flex: none; display: flex; flex-direction: column; }
        .chat-body { padding: 1.1rem; display: flex; flex-direction: column; flex: 1; gap: .9rem; min-height: 0; }

        .messages { flex: 1; display: flex; flex-direction: column; gap: .7rem; overflow-y: auto; max-height: calc(65vh - 60px); padding-right: .2rem; }
        .messages::-webkit-scrollbar { width: 4px; }
        .messages::-webkit-scrollbar-track { background: transparent; }
        .messages::-webkit-scrollbar-thumb { background: var(--line); border-radius: 4px; }

        .msg { display: flex; gap: .55rem; }
        .msg.user { flex-direction: row-reverse; }
        .avatar { width: 32px; height: 32px; border-radius: 10px; background: linear-gradient(135deg, var(--teal), var(--ocean)); color: #fff; display: grid; place-items: center; font-size: .7rem; font-weight: 800; flex: none; }
        .avatar.user-av { background: linear-gradient(135deg, var(--sand), #ef4444); }
        .bubble { background: #f8fafc; border: 1px solid var(--line); padding: .65rem .9rem; border-radius: 4px 14px 14px 14px; font-size: .86rem; line-height: 1.55; color: #334155; max-width: 88%; }
        .bubble.user-bubble { background: var(--teal); border-color: var(--teal-dark); color: #fff; border-radius: 14px 4px 14px 14px; }
        .bubble strong { color: var(--teal-dark); }
        .bubble.user-bubble strong { color: #ccfbf1; }
        .bubble p { margin: .3rem 0; }
        .bubble p:first-child { margin-top: 0; }
        .bubble p:last-child { margin-bottom: 0; }

        /* loading dots */
        .typing { display: flex; gap: 4px; align-items: center; padding: .5rem .7rem; }
        .typing span { width: 7px; height: 7px; border-radius: 50%; background: var(--muted); animation: bounce .9s infinite; }
        .typing span:nth-child(2) { animation-delay: .18s; }
        .typing span:nth-child(3) { animation-delay: .36s; }
        @keyframes bounce { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-5px); } }

        .hint { display: grid; gap: .45rem; }
        .hint button { text-align: left; font: inherit; font-size: .82rem; padding: .5rem .8rem; border-radius: 10px; border: 1px dashed var(--line); background: #fff; color: var(--teal-dark); cursor: pointer; transition: background .15s, border-color .15s; }
        .hint button:hover { background: #f0fdfa; border-color: var(--teal); }
        .hint button:disabled { cursor: not-allowed; opacity: .5; }

        .input { display: flex; gap: .5rem; }
        .input input { flex: 1; padding: .7rem .9rem; border-radius: 12px; border: 1px solid var(--line); background: #f8fafc; font: inherit; font-size: .88rem; color: var(--ink); outline: none; transition: border-color .15s; }
        .input input:focus { border-color: var(--teal); }
        .input input:disabled { cursor: not-allowed; color: var(--muted); background: #f1f5f9; }
        .send-btn { padding: .7rem 1.1rem; border-radius: 12px; border: none; background: var(--teal); color: #fff; font: inherit; font-weight: 600; cursor: pointer; transition: background .15s, opacity .15s; }
        .send-btn:hover:not(:disabled) { background: var(--teal-dark); }
        .send-btn:disabled { opacity: .5; cursor: not-allowed; }

        .status-bar { display: flex; align-items: center; gap: .4rem; font-size: .72rem; font-weight: 600; padding: .45rem .7rem; border-radius: 10px; }
        .status-bar.warning { color: var(--sand); background: #fffbeb; border: 1px solid #fde68a; }
        .status-bar.success { color: #059669; background: #ecfdf5; border: 1px solid #6ee7b7; }
        .status-bar.error-bar { color: #dc2626; background: #fef2f2; border: 1px solid #fca5a5; }

        /* rute di peta */
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 8px 24px -8px rgba(15,23,42,.25); }
        .leaflet-popup-content { font-family: 'Plus Jakarta Sans', sans-serif; margin: .8rem 1rem; line-height: 1.5; }
        .pop-nama { font-weight: 800; font-size: .95rem; }
        .pop-kat { display: inline-block; margin-top: .2rem; font-size: .7rem; font-weight: 600; color: var(--teal-dark); background: #ccfbf1; padding: .15rem .55rem; border-radius: 999px; }
        .pop-row { margin-top: .35rem; font-size: .78rem; color: #475569; }
        .pop-jarak { color: var(--ocean); font-weight: 600; }

        footer { text-align: center; padding: 1.2rem; font-size: .75rem; color: var(--muted); }

        @media (max-width: 900px) {
            .layout { flex-direction: column; padding: 0 1rem; margin-top: -2rem; }
            .chat-card { width: auto; }
            #map { height: 45vh; min-height: 320px; }
            .hero { padding: 1.4rem 1.2rem 3rem; }
            .messages { max-height: 38vh; }
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
                <span class="chip chip-lokasi" id="chip-lokasi" onclick="mintaLokasi()">
                    📍 Izinkan Lokasi
                </span>
            </div>
        </div>
    </header>

    <main class="layout">
        <section class="card map-card">
            <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Peta Interaktif — klik marker untuk detail wisata
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
                    <button onclick="kirimHint(this)">Rekomendasi pantai terdekat dari lokasi saya</button>
                    <button onclick="kirimHint(this)">Wisata yang buka sekarang</button>
                    <button onclick="kirimHint(this)">Berapa tiket Museum Adityawarman?</button>
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

    wisataData.forEach(w => {
        const marker = L.marker([Number(w.lat), Number(w.lng)]).addTo(map)
            .bindPopup(`
                <div class="pop-nama">${w.nama}</div>
                <span class="pop-kat">${w.kategori.nama}</span>
                <div class="pop-row">Tiket: ${Number(w.harga_tiket) === 0 ? 'Gratis' : 'Rp ' + Number(w.harga_tiket).toLocaleString('id-ID')}</div>
                <div class="pop-row">Jam: ${w.jam_buka.slice(0,5)}&ndash;${w.jam_tutup.slice(0,5)} WIB &middot; ⭐ ${Number(w.rating).toFixed(1)}</div>
            `);
        wisataMarkers[w.id] = marker;
    });

    // ─── Sesi ────────────────────────────────────────────────────────────────
    async function inisialisasiSesi(lat = null, lng = null) {
        try {
            const body = { session_token: sessionToken };
            if (lat !== null) { body.lat = lat; body.lng = lng; }

            const res = await fetch('/chat/session', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
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
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_token: sessionToken, pesan }),
            });

            hapusTyping();

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                tampilPesan('assistant', err.error || 'Terjadi kesalahan. Coba lagi.');
                return;
            }

            const data = await res.json();
            tampilPesan('assistant', data.jawaban);

            // Sorot marker wisata yang direkomendasikan di peta
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

    // ─── Peta: sorot hasil rekomendasi ────────────────────────────────────────
    function sorotWisataDiPeta(wisataList, adaLokasi) {
        // Hapus rute lama
        if (ruteLayer) { map.removeLayer(ruteLayer); ruteLayer = null; }

        const bounds = [];

        wisataList.forEach((w, i) => {
            const marker = wisataMarkers[w.id];
            if (marker) {
                const jarakTeks = w.jarak_km !== undefined ? `<div class="pop-row pop-jarak">🚗 ${w.jarak_km} km dari lokasi kamu</div>` : '';
                marker.setPopupContent(`
                    <div class="pop-nama">${i + 1}. ${w.nama}</div>
                    <span class="pop-kat">${w.kategori}</span>
                    <div class="pop-row">Tiket: ${w.harga_tiket === 0 ? 'Gratis' : 'Rp ' + w.harga_tiket.toLocaleString('id-ID')}</div>
                    <div class="pop-row">Jam: ${w.jam_buka || '-'}&ndash;${w.jam_tutup || '-'} WIB &middot; ⭐ ${w.rating.toFixed(1)}</div>
                    ${jarakTeks}
                `);
                if (i === 0) marker.openPopup();
                bounds.push([w.lat, w.lng]);
            }
        });

        // Jika ada lokasi user, gambar garis sederhana ke wisata terdekat via OSRM
        if (adaLokasi && userLat && wisataList.length > 0) {
            const tujuan = wisataList[0];
            tarikRuteOSRM(userLat, userLng, tujuan.lat, tujuan.lng);
            bounds.push([userLat, userLng]);
        }

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // ─── OSRM rute ───────────────────────────────────────────────────────────
    async function tarikRuteOSRM(latA, lngA, latB, lngB) {
        try {
            const url = `https://router.project-osrm.org/route/v1/driving/${lngA},${latA};${lngB},${latB}?overview=full&geometries=geojson`;
            const res  = await fetch(url);
            const data = await res.json();
            if (data.code !== 'Ok' || !data.routes[0]) return;

            if (ruteLayer) map.removeLayer(ruteLayer);
            ruteLayer = L.geoJSON(data.routes[0].geometry, {
                style: { color: '#0369a1', weight: 4, opacity: .75, dashArray: '8 6' }
            }).addTo(map);
        } catch (e) {
            // OSRM gagal — tidak blokir fungsi utama
        }
    }

    // ─── UI helpers ──────────────────────────────────────────────────────────
    function tampilPesan(role, teks) {
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
        } else {
            div.innerHTML = `
                <div class="avatar">AI</div>
                <div class="bubble">${html}</div>`;
        }
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function tampilTyping() {
        const container = document.getElementById('messages');
        const div = document.createElement('div');
        div.className = 'msg';
        div.id = 'typing-indicator';
        div.innerHTML = `<div class="avatar">AI</div><div class="bubble"><div class="typing"><span></span><span></span><span></span></div></div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function hapusTyping() {
        document.getElementById('typing-indicator')?.remove();
    }

    function sembunyikanHint() {
        const hint = document.getElementById('hint');
        if (hint) hint.style.display = 'none';
    }

    function setKirimDisabled(disabled) {
        sedangKirim = disabled;
        document.getElementById('send-btn').disabled = disabled;
        document.getElementById('input-pesan').disabled = disabled;
        if (!disabled) document.getElementById('input-pesan').focus();
    }

    function setStatus(tipe, teks) {
        const bar  = document.getElementById('status-bar');
        const span = document.getElementById('status-text');
        bar.className = 'status-bar';
        if (tipe === 'success') bar.classList.add('success');
        else if (tipe === 'error') bar.classList.add('error-bar');
        else bar.classList.add('warning');
        span.textContent = teks;
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

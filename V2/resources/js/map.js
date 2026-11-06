import '../css/map.css';

/*
 * Интерактивная карта курорта: зум и перемещение (мышь, колесо, касания, щипок),
 * метки с карточками, переключение зима/лето, «вы здесь» по GPS.
 * Метки — обычные кнопки в разметке страницы (работают и до загрузки скрипта).
 */
const root = document.querySelector('[data-map-root]');

if (root) {
    const vp = root.querySelector('[data-viewport]');
    const stage = root.querySelector('[data-stage]');
    const W = Number(root.dataset.w);
    const H = Number(root.dataset.h);
    const card = root.querySelector('[data-card]');
    const toastEl = root.querySelector('[data-toast]');
    const me = root.querySelector('[data-me]');
    const i18n = JSON.parse(root.querySelector('[data-map-i18n]')?.textContent || '{}');
    const geoPoints = JSON.parse(root.querySelector('[data-map-geo]')?.textContent || '{}').points || [];

    let s = 1, tx = 0, ty = 0, minS = 0.2, maxS = 2.4, moved = 0, activeId = null, toastTimer = null;
    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const store = {
        get: (k) => { try { return localStorage.getItem(k); } catch { return null; } },
        set: (k, v) => { try { localStorage.setItem(k, v); } catch { /* приватный режим */ } },
    };

    const limits = () => {
        // минимум — «обложка»: карта всегда закрывает область просмотра, без пустых полос и с возможностью двигать
        minS = Math.max(vp.clientWidth / W, vp.clientHeight / H);
        maxS = Math.max(2.4, minS * 2.5);
    };
    const apply = () => {
        const vw = vp.clientWidth, vh = vp.clientHeight, sw = W * s, sh = H * s;
        tx = sw <= vw ? (vw - sw) / 2 : clamp(tx, vw - sw, 0);
        ty = sh <= vh ? (vh - sh) / 2 : clamp(ty, vh - sh, 0);
        stage.style.transform = `translate(${tx}px,${ty}px) scale(${s})`;
        stage.style.setProperty('--s', s);
        vp.classList.toggle('is-zoomed', s >= 0.95);
    };
    const animate = (fn) => {
        stage.classList.add('is-anim');
        fn();
        setTimeout(() => stage.classList.remove('is-anim'), 480);
    };
    const zoomAt = (factor, cx, cy) => {
        const ns = clamp(s * factor, minS, maxS);
        tx = cx - ((cx - tx) * ns) / s;
        ty = cy - ((cy - ty) * ns) / s;
        s = ns;
        apply();
    };
    const centerOn = (x, y, scale = s, offsetY = 0) => {
        s = clamp(scale, minS, maxS);
        tx = vp.clientWidth / 2 - x * s;
        ty = vp.clientHeight / 2 - y * s + offsetY;
        apply();
    };
    const fit = (initial = false) => {
        limits();
        s = minS;
        centerOn(W * 0.47, H * 0.44, s);
    };

    // ——— сезон ———
    const layers = [...stage.querySelectorAll('[data-layer]')];
    const loadFull = (layer) => {
        if (!layer?.dataset.full || layer.dataset.loaded) return;
        layer.dataset.loaded = '1';
        const img = new Image();
        img.onload = () => { layer.src = layer.dataset.full; };
        img.src = layer.dataset.full;
    };
    const visible = (el) => el.offsetParent !== null || getComputedStyle(el).position === 'fixed';
    const setSeason = (season, remember = true) => {
        root.dataset.season = season;
        layers.forEach((l) => l.toggleAttribute('data-off', l.dataset.layer !== season));
        root.querySelectorAll('[data-season-btn]').forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.seasonBtn === season)));
        loadFull(layers.find((l) => l.dataset.layer === season));
        if (remember) store.set('tenir_map_season', season);
        if (activeId && !visible(document.getElementById('pin-' + activeId))) hideCard();
    };
    root.querySelectorAll('[data-season-btn]').forEach((b) => b.addEventListener('click', () => setSeason(b.dataset.seasonBtn)));

    // ——— карточка ———
    const hideCard = () => {
        card.hidden = true;
        document.getElementById('pin-' + activeId)?.classList.remove('is-active');
        activeId = null;
    };
    const select = (id, { pan = true } = {}) => {
        const pin = document.getElementById('pin-' + id);
        if (!pin) return;
        const seasons = pin.dataset.seasons;
        if (seasons && !seasons.split(' ').includes(root.dataset.season)) setSeason(seasons.split(' ')[0]);
        document.getElementById('pin-' + activeId)?.classList.remove('is-active');
        activeId = id;
        pin.classList.add('is-active');
        card.querySelector('[data-card-kind]').textContent = pin.dataset.kindLabel || '';
        card.querySelector('[data-card-name]').textContent = pin.dataset.name || '';
        const note = card.querySelector('[data-card-note]');
        note.textContent = pin.dataset.note || '';
        note.hidden = !pin.dataset.note;
        const link = card.querySelector('[data-card-link]');
        link.hidden = !pin.dataset.href;
        if (pin.dataset.href) { link.href = pin.dataset.href; link.textContent = pin.dataset.hrefLabel; }
        card.hidden = false;
        if (pan) {
            const x = parseFloat(pin.style.left), y = parseFloat(pin.style.top);
            // карточка занимает низ области просмотра: метку ставим в верхнюю половину
            animate(() => centerOn(x, y, Math.max(s, Math.min(1, maxS)), vp.clientHeight * 0.18));
        }
    };
    card.querySelector('[data-card-close]').addEventListener('click', hideCard);
    root.querySelectorAll('[data-focus]').forEach((b) => b.addEventListener('click', () => {
        vp.scrollIntoView({ behavior: 'smooth', block: 'center' });
        select(b.dataset.focus);
    }));

    // ——— жесты ———
    const pointers = new Map();
    let last = null;
    const dist = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
    const rel = (e) => { const r = vp.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; };

    vp.addEventListener('pointerdown', (e) => {
        if (e.target.closest('[data-card], .map-controls')) return;
        pointers.set(e.pointerId, rel(e));
        moved = 0;
        last = null;
        vp.classList.add('is-drag');
    });
    window.addEventListener('pointermove', (e) => {
        if (!pointers.has(e.pointerId)) return;
        const prev = pointers.get(e.pointerId), cur = rel(e);
        pointers.set(e.pointerId, cur);
        if (pointers.size === 1) {
            tx += cur.x - prev.x;
            ty += cur.y - prev.y;
            moved += Math.abs(cur.x - prev.x) + Math.abs(cur.y - prev.y);
            apply();
        } else if (pointers.size === 2) {
            const [a, b] = [...pointers.values()];
            const d = dist(a, b), mid = { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 };
            if (last) {
                tx += mid.x - last.mid.x;
                ty += mid.y - last.mid.y;
                zoomAt(d / last.d, mid.x, mid.y);
            }
            last = { d, mid };
            moved = 99;
        }
    });
    const release = (e) => {
        pointers.delete(e.pointerId);
        last = null;
        if (pointers.size === 0) vp.classList.remove('is-drag');
    };
    window.addEventListener('pointerup', release);
    window.addEventListener('pointercancel', release);

    // клик по метке — только если это не перетаскивание
    vp.addEventListener('click', (e) => {
        if (e.target.closest('[data-card], .map-controls')) return;
        if (moved > 8) { e.preventDefault(); e.stopPropagation(); moved = 0; return; }
        const pin = e.target.closest('[data-pin]');
        if (pin) select(pin.dataset.pin);
        else hideCard();
    }, true);

    vp.addEventListener('wheel', (e) => {
        e.preventDefault();
        const p = rel(e);
        zoomAt(Math.exp(-e.deltaY * (e.ctrlKey ? 0.01 : 0.0016)), p.x, p.y);
    }, { passive: false });
    vp.addEventListener('dblclick', (e) => {
        if (e.target.closest('[data-pin], [data-card], .map-controls')) return;
        const p = rel(e);
        animate(() => zoomAt(1.8, p.x, p.y));
    });
    vp.addEventListener('keydown', (e) => {
        const step = 60;
        const act = { '+': () => zoomAt(1.3, vp.clientWidth / 2, vp.clientHeight / 2), '=': () => zoomAt(1.3, vp.clientWidth / 2, vp.clientHeight / 2),
            '-': () => zoomAt(1 / 1.3, vp.clientWidth / 2, vp.clientHeight / 2),
            ArrowLeft: () => { tx += step; apply(); }, ArrowRight: () => { tx -= step; apply(); },
            ArrowUp: () => { ty += step; apply(); }, ArrowDown: () => { ty -= step; apply(); }, Escape: hideCard }[e.key];
        if (act) { e.preventDefault(); act(); }
    });
    root.querySelectorAll('[data-zoom]').forEach((b) => b.addEventListener('click', () => {
        const cx = vp.clientWidth / 2, cy = vp.clientHeight / 2;
        animate(() => {
            if (b.dataset.zoom === 'fit') fit(true);
            else zoomAt(b.dataset.zoom === 'in' ? 1.6 : 1 / 1.6, cx, cy);
        });
    }));

    // ——— «вы здесь» ———
    const toast = (msg) => {
        toastEl.textContent = msg;
        toastEl.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toastEl.hidden = true; }, 4500);
    };
    // Привязка: аффинное преобразование по методу наименьших квадратов (3 точки — точное, больше — усреднение)
    const lat0 = geoPoints.length ? geoPoints[0][0] : 0, lon0 = geoPoints.length ? geoPoints[0][1] : 0;
    const local = (lat, lon) => [(lon - lon0) * Math.cos((lat0 * Math.PI) / 180) * 111.32, (lat - lat0) * 110.57]; // км
    const solve = (A, b) => { // метод Гаусса 3×3
        const m = A.map((r, i) => [...r, b[i]]);
        for (let i = 0; i < 3; i++) {
            let p = i;
            for (let r = i + 1; r < 3; r++) if (Math.abs(m[r][i]) > Math.abs(m[p][i])) p = r;
            [m[i], m[p]] = [m[p], m[i]];
            if (Math.abs(m[i][i]) < 1e-9) return null;
            for (let r = 0; r < 3; r++) {
                if (r === i) continue;
                const f = m[r][i] / m[i][i];
                for (let c = i; c < 4; c++) m[r][c] -= f * m[i][c];
            }
        }
        return m.map((r, i) => r[3] / r[i]);
    };
    let affine = null;
    if (geoPoints.length >= 3) {
        const rows = geoPoints.map((p) => [...local(p[0], p[1]), 1]);
        const ata = [0, 1, 2].map((i) => [0, 1, 2].map((j) => rows.reduce((a, r) => a + r[i] * r[j], 0)));
        const atb = (k) => [0, 1, 2].map((i) => rows.reduce((a, r, n) => a + r[i] * geoPoints[n][k], 0));
        const cx = solve(ata, atb(2)), cy = solve(ata, atb(3));
        if (cx && cy) affine = { cx, cy };
    }
    const km = (lat1, lon1, lat2, lon2) => { const a = local(lat1, lon1), b = local(lat2, lon2); return Math.hypot(a[0] - b[0], a[1] - b[1]); };

    const locate = root.querySelector('[data-locate]');
    locate?.addEventListener('click', () => {
        if (!navigator.geolocation || !affine) return toast(i18n.unavailable);
        toast(i18n.locating);
        navigator.geolocation.getCurrentPosition((pos) => {
            const { latitude: lat, longitude: lon } = pos.coords;
            const nearest = Math.min(...geoPoints.map((p) => km(lat, lon, p[0], p[1])));
            const [e, n] = local(lat, lon);
            const x = affine.cx[0] * e + affine.cx[1] * n + affine.cx[2];
            const y = affine.cy[0] * e + affine.cy[1] * n + affine.cy[2];
            if (nearest > 6 || x < -120 || y < -120 || x > W + 120 || y > H + 120) {
                me.hidden = true;
                return toast(i18n.far);
            }
            me.style.left = clamp(x, 0, W) + 'px';
            me.style.top = clamp(y, 0, H) + 'px';
            me.hidden = false;
            vp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            animate(() => centerOn(clamp(x, 0, W), clamp(y, 0, H), Math.max(s, 0.9)));
            toast(i18n.here);
        }, (err) => toast(err.code === 1 ? i18n.denied : i18n.unavailable), { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 });
    });

    // ——— старт ———
    const month = new Date().getMonth() + 1;
    const stored = store.get('tenir_map_season');
    setSeason(stored === 'winter' || stored === 'summer' ? stored : ([11, 12, 1, 2, 3, 4].includes(month) ? 'winter' : 'summer'), false);
    fit(true);
    window.addEventListener('resize', () => { limits(); apply(); });
    const warm = layers.find((l) => l.dataset.layer !== root.dataset.season);
    window.addEventListener('load', () => setTimeout(() => loadFull(warm), 2500));
}

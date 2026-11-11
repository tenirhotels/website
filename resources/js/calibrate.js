import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '../css/calibrate.css';
import { fitReport, buildInverse, meters } from './geofit';

/*
 * Страница «Привязка карты» (админка): слева иллюстрация, справа спутниковый снимок (Esri).
 * Кликаете одно и то же место на обеих — получается опорная точка. По ним строится та же привязка,
 * что и «Вы здесь» на сайте (thin-plate spline), поэтому ошибки в метрах здесь — настоящие.
 */

const ESRI = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
const TOPO = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
const DUP_M = 100; // как на сайте: отдельная точка ближе 100 м к метке считается дублем метки

/** Аффинное преобразование по трём парам точек: (sx,sy) → (x,y); возвращает [a,b,c,d,e,f] для ctx.transform. */
export function affine(s, d) {
    const [[sx0, sy0], [sx1, sy1], [sx2, sy2]] = s;
    const det = sx0 * (sy1 - sy2) + sx1 * (sy2 - sy0) + sx2 * (sy0 - sy1);
    if (Math.abs(det) < 1e-9) return null;
    const row = (v0, v1, v2) => [
        (v0 * (sy1 - sy2) + v1 * (sy2 - sy0) + v2 * (sy0 - sy1)) / det,
        (sx0 * (v1 - v2) + sx1 * (v2 - v0) + sx2 * (v0 - v1)) / det,
        (v0 * (sx1 * sy2 - sx2 * sy1) - v1 * (sx0 * sy2 - sx2 * sy0) + v2 * (sx0 * sy1 - sx1 * sy0)) / det,
    ];
    const [a, c, e] = row(d[0][0], d[1][0], d[2][0]);
    const [b, dd, f] = row(d[0][1], d[1][1], d[2][1]);
    return [a, b, c, dd, e, f];
}

/** Слой на карте: иллюстрация, перекошенная по привязке (сетка из треугольников). */
const Warp = L.Layer.extend({
    initialize(img, opacity) { this._img = img; this._op = opacity; this._inv = null; },
    onAdd(map) {
        this._map = map;
        this._c = L.DomUtil.create('canvas', 'cal-warp');
        this._c.style.opacity = this._op;
        map.getPane('overlayPane').appendChild(this._c);
        map.on('moveend zoomend resize', this._draw, this);
        this._draw();
    },
    onRemove(map) { map.off('moveend zoomend resize', this._draw, this); L.DomUtil.remove(this._c); },
    setData(inv, W, H) { this._inv = inv; this._W = W; this._H = H; if (this._map) this._draw(); },
    setOpacity(o) { this._op = o; if (this._c) this._c.style.opacity = o; },
    _draw() {
        const m = this._map, c = this._c, size = m.getSize();
        c.width = size.x; c.height = size.y;
        L.DomUtil.setPosition(c, m.containerPointToLayerPoint([0, 0]));
        const ctx = c.getContext('2d');
        ctx.clearRect(0, 0, c.width, c.height);
        if (!this._inv || !this._img.complete || !this._img.naturalWidth) return;
        const N = 40, M = Math.max(8, Math.round(N * this._H / this._W));
        const sw = this._img.naturalWidth / this._W, sh = this._img.naturalHeight / this._H;
        const v = [];
        for (let j = 0; j <= M; j++) {
            v[j] = [];
            for (let i = 0; i <= N; i++) {
                const x = (i / N) * this._W, y = (j / M) * this._H;
                const ll = this._inv(x, y), p = m.latLngToContainerPoint(L.latLng(ll[0], ll[1]));
                v[j][i] = { s: [x * sw, y * sh], d: [p.x, p.y] };
            }
        }
        const tri = (a, b, e) => {
            const t = affine([a.s, b.s, e.s], [a.d, b.d, e.d]);
            if (!t) return;
            ctx.save();
            ctx.beginPath(); ctx.moveTo(a.d[0], a.d[1]); ctx.lineTo(b.d[0], b.d[1]); ctx.lineTo(e.d[0], e.d[1]); ctx.closePath(); ctx.clip();
            ctx.transform(...t);
            ctx.drawImage(this._img, 0, 0);
            ctx.restore();
        };
        for (let j = 0; j < M; j++) for (let i = 0; i < N; i++) {
            tri(v[j][i], v[j][i + 1], v[j + 1][i]);
            tri(v[j][i + 1], v[j + 1][i + 1], v[j + 1][i]);
        }
    },
});

export function initCalibration(root, cfg, save) {
    if (root.__cal) return;
    root.__cal = true;
    const W = cfg.width, H = cfg.height;
    const pins = cfg.pins; // [{id,label,lat,lng,x,y}]
    const ignore = new Set(cfg.ignore || []);
    let anchors = (cfg.anchors || []).map((a) => ({ lat: +a.lat, lng: +a.lng, x: +a.x, y: +a.y }));
    let pending = { img: null, geo: null };
    let sel = null, dirty = false, overlayOn = false;

    const $ = (s) => root.querySelector(s);
    const toLL = (x, y) => L.latLng(H - y, x);
    const img = new Image();
    img.src = cfg.image;

    // Левая панель — иллюстрация
    const mi = L.map($('[data-img]'), { crs: L.CRS.Simple, minZoom: -3, maxZoom: 3, zoomSnap: 0.25, attributionControl: false });
    L.imageOverlay(cfg.image, [[0, 0], [H, W]]).addTo(mi);
    mi.fitBounds([[0, 0], [H, W]]);

    // Правая панель — спутник
    const mg = L.map($('[data-geo]'), { maxZoom: 20, zoomSnap: 0.5, zoomAnimation: false, markerZoomAnimation: false });
    const sat = L.tileLayer(ESRI, { maxNativeZoom: 18, maxZoom: 20, attribution: 'Esri, Maxar, Earthstar Geographics' }).addTo(mg);
    const topo = L.tileLayer(TOPO, { maxNativeZoom: 17, maxZoom: 20, subdomains: 'abc', attribution: 'OpenTopoMap, OSM' });
    L.control.layers({ 'Спутник (Esri)': sat, 'Топо (OpenTopoMap)': topo }, {}, { collapsed: true }).addTo(mg);
    const lats = pins.filter((p) => p.lat).map((p) => [p.lat, p.lng]);
    mg.setView(lats.length ? L.latLngBounds(lats).getCenter() : [43.13, 77.09], 13);
    if (lats.length) mg.fitBounds(L.latLngBounds(lats).pad(0.15));

    const warp = new Warp(img, 0.6);
    img.onload = () => redraw();

    const icon = (cls, n) => L.divIcon({ className: `cal-pin ${cls}`, html: `<span>${n}</span>`, iconSize: [22, 22], iconAnchor: [11, 11] });
    let layers = [];

    /** Все точки для таблицы: метки с координатами, затем отдельные точки. use = участвует в подгонке. */
    const rows = () => {
        const out = [];
        pins.forEach((p) => out.push({ kind: 'pin', id: p.id, label: p.label, lat: p.lat, lng: p.lng, x: p.x, y: p.y, use: !ignore.has(p.id), note: ignore.has(p.id) ? 'отключена' : '' }));
        const active = out.filter((r) => r.use);
        anchors.forEach((a, i) => {
            const dup = active.find((p) => meters([a.lat, a.lng], [p.lat, p.lng]) < DUP_M);
            out.push({ kind: 'anchor', idx: i, label: `точка ${i + 1}`, lat: a.lat, lng: a.lng, x: a.x, y: a.y, use: !dup, note: dup ? `рядом с меткой «${dup.label}» — не учитывается; отключите метку или сдвиньте точку` : '' });
        });
        return out;
    };

    const fmt = (v) => (v === null || v === undefined ? '—' : v < 10 ? v.toFixed(1) : Math.round(v));
    const cls = (v) => (v === null ? '' : v <= 30 ? 'cal-good' : v <= 100 ? 'cal-mid' : 'cal-bad');

    function redraw() {
        layers.forEach((l) => l.remove());
        layers = [];
        const all = rows();
        const used = all.filter((r) => r.use && r.lat && r.lng);
        const rep = used.length >= 3 ? fitReport(used.map((r) => [r.lat, r.lng, r.x, r.y])) : null;
        used.forEach((r, i) => { r.rep = rep ? rep.rows[i] : null; });

        all.forEach((r, n) => {
            const c = r.kind === 'pin' ? (r.use ? 'cal-pin--fixed' : 'cal-pin--off') : r.use ? 'cal-pin--anchor' : 'cal-pin--off';
            const k = `${c} ${sel === r.id + ':' + r.idx ? 'cal-pin--sel' : ''}`;
            if (!r.lat) return;
            const a = L.marker(toLL(r.x, r.y), { icon: icon(k, n + 1), draggable: r.kind === 'anchor', title: r.label }).addTo(mi);
            const b = L.marker([r.lat, r.lng], { icon: icon(k, n + 1), draggable: r.kind === 'anchor', title: r.label }).addTo(mg);
            if (r.kind === 'anchor') {
                a.on('dragend', () => { const p = a.getLatLng(); anchors[r.idx].x = Math.round(p.lng); anchors[r.idx].y = Math.round(H - p.lat); touch(); });
                b.on('dragend', () => { const p = b.getLatLng(); anchors[r.idx].lat = +p.lat.toFixed(6); anchors[r.idx].lng = +p.lng.toFixed(6); touch(); });
            }
            layers.push(a, b);
        });
        if (pending.img) layers.push(L.marker(toLL(pending.img.x, pending.img.y), { icon: icon('cal-pin--pending', '?'), interactive: false }).addTo(mi));
        if (pending.geo) layers.push(L.marker([pending.geo.lat, pending.geo.lng], { icon: icon('cal-pin--pending', '?'), interactive: false }).addTo(mg));

        // Таблица
        $('[data-rows]').innerHTML = all.map((r, n) => `
            <tr data-row="${n}" class="${r.use ? '' : 'is-off'}">
              <td>${n + 1}</td>
              <td>${r.kind === 'pin' ? `<label><input type="checkbox" data-use="${r.id}" ${r.use ? 'checked' : ''}> ${esc(r.label)}</label>` : esc(r.label)}${r.note ? `<div style="font-size:12px;color:#b3261e">${esc(r.note)}</div>` : ''}</td>
              <td class="${cls(r.rep?.residual ?? null)}">${fmt(r.rep?.residual ?? null)}</td>
              <td class="${cls(r.rep?.loo ?? null)}">${fmt(r.rep?.loo ?? null)}</td>
              <td>${r.kind === 'anchor' ? `<button type="button" data-del="${r.idx}">Удалить</button>` : ''}</td>
            </tr>`).join('');

        $('[data-sum]').innerHTML = rep
            ? `Опорных точек: <b>${used.length}</b> · перекрёстная проверка (точка исключена из подгонки): медиана <b class="${cls(rep.looMedian)}">${fmt(rep.looMedian)} м</b>, максимум <b class="${cls(rep.looMax)}">${fmt(rep.looMax)} м</b>`
            : `Опорных точек: ${used.length}. Нужно минимум 3, лучше 8 и больше.`;

        $('[data-save]').disabled = !dirty;
        const hint = $('[data-hint]');
        hint.className = 'cal-hint' + (pending.img || pending.geo ? ' is-pending' : '');
        hint.textContent = pending.img && !pending.geo ? 'Теперь кликните то же место на спутниковом снимке (справа).'
            : pending.geo && !pending.img ? 'Теперь кликните то же место на иллюстрации (слева).'
            : 'Кликните узнаваемое место на иллюстрации и то же место на снимке (в любом порядке). Хорошие точки: станции, повороты подъёмников и дорог, углы зданий. Перетаскивайте оранжевые точки, чтобы уточнить.';

        // Наложение
        warp.setData(rep ? rep.inverse : null, W, H);
        $('[data-overlay-note]').textContent = rep ? '' : 'Наложение появится, когда будет хотя бы 3 точки.';
    }

    const touch = () => { dirty = true; redraw(); };
    const esc = (s) => String(s ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

    function tryComplete() {
        if (pending.img && pending.geo) {
            anchors.push({ lat: +pending.geo.lat.toFixed(6), lng: +pending.geo.lng.toFixed(6), x: Math.round(pending.img.x), y: Math.round(pending.img.y) });
            pending = { img: null, geo: null };
            dirty = true;
        }
        redraw();
    }
    mi.on('click', (e) => { pending.img = { x: Math.max(0, Math.min(W, e.latlng.lng)), y: Math.max(0, Math.min(H, H - e.latlng.lat)) }; tryComplete(); });
    mg.on('click', (e) => { pending.geo = { lat: e.latlng.lat, lng: e.latlng.lng }; tryComplete(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && (pending.img || pending.geo)) { pending = { img: null, geo: null }; redraw(); } });

    root.addEventListener('click', (e) => {
        const del = e.target.closest('[data-del]');
        if (del) { anchors.splice(+del.dataset.del, 1); touch(); return; }
        const tr = e.target.closest('[data-row]');
        if (tr && !e.target.closest('input,button,label')) {
            const r = rows()[+tr.dataset.row];
            if (r?.lat) { mi.setView(toLL(r.x, r.y), Math.max(mi.getZoom(), 0.5)); mg.setView([r.lat, r.lng], Math.max(mg.getZoom(), 16)); }
        }
    });
    root.addEventListener('change', (e) => {
        const u = e.target.closest('[data-use]');
        if (u) { if (u.checked) ignore.delete(u.dataset.use); else ignore.add(u.dataset.use); touch(); }
    });
    $('[data-undo]').addEventListener('click', () => { if (anchors.length) { anchors.pop(); touch(); } });
    $('[data-save]').addEventListener('click', async () => {
        $('[data-save]').disabled = true;
        await save(anchors, [...ignore]);
        dirty = false; redraw();
    });
    $('[data-overlay]').addEventListener('change', (e) => {
        overlayOn = e.target.checked;
        if (overlayOn) warp.addTo(mg); else mg.removeLayer(warp);
        redraw();
    });
    $('[data-opacity]').addEventListener('input', (e) => warp.setOpacity(e.target.value / 100));
    window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

    redraw();
    setTimeout(() => { mi.invalidateSize(); mg.invalidateSize(); }, 200);
}

window.TenirCalibrate = { init: initCalibration };

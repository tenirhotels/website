/*
 * Галерея: полноэкранный просмотр с листанием (стрелки, клавиши, свайп), увеличением (двойной тап, колесо, щипок)
 * и миниатюрами. Разметка — компонент x-site.gallery; здесь только поведение.
 */
let box;
let state = { items: [], i: 0, scale: 1, x: 0, y: 0 };
let opener = null;
const pointers = new Map();
let pinch = 0;
let startX = 0;
let startY = 0;
let moved = false;
let lastTap = 0;

const $ = (sel) => box.querySelector(sel);

function build(labels) {
    box = document.createElement('div');
    box.className = 'lb';
    box.hidden = true;
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.innerHTML = `
        <div class="lb-top"><span class="lb-count" aria-live="polite"></span>
            <span class="lb-actions">
                <button type="button" class="lb-btn" data-lb="zoom" aria-label="${labels.zoom}">+</button>
                <button type="button" class="lb-btn" data-lb="close" aria-label="${labels.close}">&times;</button>
            </span></div>
        <div class="lb-stage"><img class="lb-img" alt="" draggable="false"></div>
        <button type="button" class="lb-nav lb-prev" data-lb="prev" aria-label="${labels.prev}">&#8249;</button>
        <button type="button" class="lb-nav lb-next" data-lb="next" aria-label="${labels.next}">&#8250;</button>
        <div class="lb-thumbs"></div>`;
    document.body.appendChild(box);

    box.addEventListener('click', (e) => {
        const act = e.target.closest('[data-lb]')?.dataset.lb;
        if (act === 'close') close();
        else if (act === 'prev') go(-1);
        else if (act === 'next') go(1);
        else if (act === 'zoom') zoomAt(state.scale > 1 ? 1 : 2.4);
        else if (e.target.closest('.lb-thumb')) show(Number(e.target.closest('.lb-thumb').dataset.i));
        else if (e.target.classList.contains('lb-stage')) close();
    });

    const stage = $('.lb-stage');
    stage.addEventListener('pointerdown', (e) => {
        stage.setPointerCapture(e.pointerId);
        pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
        startX = e.clientX; startY = e.clientY; moved = false;
        if (pointers.size === 2) pinch = dist();
    });
    stage.addEventListener('pointermove', (e) => {
        const p = pointers.get(e.pointerId);
        if (!p) return;
        const dx = e.clientX - p.x;
        const dy = e.clientY - p.y;
        pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
        if (Math.abs(e.clientX - startX) + Math.abs(e.clientY - startY) > 6) moved = true;
        if (pointers.size === 2) {
            const d = dist();
            if (pinch) zoomAt(state.scale * (d / pinch), false);
            pinch = d;
        } else if (state.scale > 1) {
            state.x += dx; state.y += dy; clamp(); paint();
        }
    });
    const up = (e) => {
        if (!pointers.has(e.pointerId)) return;
        pointers.delete(e.pointerId);
        pinch = 0;
        if (pointers.size) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        if (state.scale <= 1.02 && moved) {
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) go(dx < 0 ? 1 : -1);
            else if (dy > 90 && Math.abs(dy) > Math.abs(dx)) close();
        } else if (!moved && e.target.classList.contains('lb-img')) {
            const now = Date.now();
            if (now - lastTap < 320) zoomAt(state.scale > 1 ? 1 : 2.4, true, e);
            lastTap = now;
        }
    };
    stage.addEventListener('pointerup', up);
    stage.addEventListener('pointercancel', up);
    stage.addEventListener('wheel', (e) => {
        e.preventDefault();
        zoomAt(state.scale * (e.deltaY < 0 ? 1.15 : 0.87), false);
    }, { passive: false });

    document.addEventListener('keydown', (e) => {
        if (box.hidden) return;
        if (e.key === 'Escape') close();
        else if (e.key === 'ArrowLeft') go(-1);
        else if (e.key === 'ArrowRight') go(1);
        else if (e.key === '+' || e.key === '=') zoomAt(state.scale * 1.3, false);
        else if (e.key === '-') zoomAt(state.scale / 1.3, false);
        else if (e.key === 'Tab') {
            const f = [...box.querySelectorAll('button')].filter((b) => b.offsetParent);
            const first = f[0]; const last = f[f.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    });
}

const dist = () => {
    const [a, b] = [...pointers.values()];
    return Math.hypot(a.x - b.x, a.y - b.y) || 1;
};

function clamp() {
    const img = $('.lb-img');
    const mx = Math.max(0, (img.clientWidth * state.scale - $('.lb-stage').clientWidth) / 2);
    const my = Math.max(0, (img.clientHeight * state.scale - $('.lb-stage').clientHeight) / 2);
    state.x = Math.max(-mx, Math.min(mx, state.x));
    state.y = Math.max(-my, Math.min(my, state.y));
}

function paint() {
    const img = $('.lb-img');
    img.style.transform = `translate(${state.x}px, ${state.y}px) scale(${state.scale})`;
    img.classList.toggle('is-zoomed', state.scale > 1.02);
    $('[data-lb="zoom"]').textContent = state.scale > 1.02 ? '−' : '+';
}

function zoomAt(scale, _animate = true, e = null) {
    state.scale = Math.max(1, Math.min(4, scale));
    if (state.scale === 1) { state.x = 0; state.y = 0; }
    else if (e) {
        const r = $('.lb-img').getBoundingClientRect();
        state.x = -(e.clientX - (r.left + r.width / 2)) * (state.scale - 1) * 0.5;
        state.y = -(e.clientY - (r.top + r.height / 2)) * (state.scale - 1) * 0.5;
    }
    clamp();
    paint();
}

function show(i) {
    const n = state.items.length;
    state.i = (i + n) % n;
    state.scale = 1; state.x = 0; state.y = 0;
    const it = state.items[state.i];
    const img = $('.lb-img');
    img.classList.add('is-loading');
    img.onload = () => img.classList.remove('is-loading');
    img.src = it.src;
    img.alt = it.alt;
    paint();
    $('.lb-count').textContent = `${state.i + 1} / ${n}`;
    box.querySelectorAll('.lb-thumb').forEach((t) => t.classList.toggle('is-on', Number(t.dataset.i) === state.i));
    box.querySelector('.lb-thumb.is-on')?.scrollIntoView({ block: 'nearest', inline: 'center' });
    // подгружаем соседние
    [state.i + 1, state.i - 1].forEach((k) => { const o = state.items[(k + n) % n]; if (o) new Image().src = o.src; });
}

const go = (d) => { if (state.items.length > 1) show(state.i + d); };

function open(group, index, trigger) {
    const labels = { close: group.dataset.lClose, prev: group.dataset.lPrev, next: group.dataset.lNext, zoom: group.dataset.lZoom };
    if (!box) build(labels);
    opener = trigger;
    state.items = [...group.querySelectorAll('[data-gallery-item]')].map((b) => ({ src: b.dataset.src, alt: b.dataset.alt || '', thumb: b.dataset.thumb || b.dataset.src }));
    box.setAttribute('aria-label', group.getAttribute('aria-label') || '');
    const multi = state.items.length > 1;
    box.classList.toggle('is-single', !multi);
    $('.lb-thumbs').innerHTML = multi ? state.items.map((it, i) => `<button type="button" class="lb-thumb" data-i="${i}" aria-label="${i + 1}"><img src="${it.thumb}" alt="" loading="lazy" draggable="false"></button>`).join('') : '';
    box.hidden = false;
    document.documentElement.classList.add('lb-open');
    show(index);
    $('[data-lb="close"]').focus();
}

function close() {
    box.hidden = true;
    document.documentElement.classList.remove('lb-open');
    opener?.focus?.();
}

document.addEventListener('click', (e) => {
    const item = e.target.closest('[data-gallery-item]');
    if (!item) return;
    const group = item.closest('[data-gallery]');
    const all = [...group.querySelectorAll('[data-gallery-item]')];
    open(group, all.indexOf(item), item);
});

// Стрелки слайдера
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-gallery-scroll]');
    if (!btn) return;
    const track = btn.closest('[data-gallery]').querySelector('[data-gallery-track]');
    track.scrollBy({ left: Number(btn.dataset.galleryScroll) * track.clientWidth * 0.8, behavior: 'smooth' });
});

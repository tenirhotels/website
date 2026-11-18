// Страница локации: погода (JSON с сервера) и камеры (обновление снимка, онлайн-трансляция по клику).
const ICONS = {
    clear: '<circle cx="12" cy="12" r="4.5"/><path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.3 5.3l1.6 1.6M17.1 17.1l1.6 1.6M18.7 5.3l-1.6 1.6M6.9 17.1l-1.6 1.6"/>',
    mostly_clear: '<circle cx="9" cy="9" r="3.5"/><path d="M9 2.5v1.6M2.5 9h1.6M4.4 4.4l1.1 1.1M13.6 4.4l-1.1 1.1"/><path d="M8 19h9a3.5 3.5 0 0 0 .4-7 5 5 0 0 0-9.3 1.5A3 3 0 0 0 8 19z"/>',
    cloudy: '<path d="M7 19h10.5a4 4 0 0 0 .5-8 6 6 0 0 0-11.5 1.6A3.3 3.3 0 0 0 7 19z"/>',
    fog: '<path d="M4 9h16M3 13h18M5 17h14"/>',
    drizzle: '<path d="M7 14h10.5a3.5 3.5 0 0 0 .4-7 5.5 5.5 0 0 0-10.600 1.400A2.900 2.900 0 0 0 7 14z"/><path d="M8 17.5l-.8 2M12 17.5l-.8 2M16 17.5l-.8 2"/>',
    rain: '<path d="M7 13h10.500a3.500 3.500 0 0 0 .4-7 5.500 5.500 0 0 0-10.600 1.400A2.900 2.900 0 0 0 7 13z"/><path d="M8 16l-1.200 4M12.500 16l-1.200 4M17 16l-1.200 4"/>',
    snow: '<path d="M7 13h10.500a3.500 3.500 0 0 0 .4-7 5.500 5.500 0 0 0-10.600 1.400A2.900 2.900 0 0 0 7 13z"/><path d="M8 17v.01M12 19v.01M16 17v.01M10 21v.01M14 21v.01"/>',
    thunder: '<path d="M7 13h10.500a3.500 3.500 0 0 0 .4-7 5.500 5.500 0 0 0-10.600 1.400A2.900 2.900 0 0 0 7 13z"/><path d="M12.500 13l-2.500 4h3l-2 4"/>',
};

function kind(code) {
    if (code === 0) return 'clear';
    if (code <= 2) return 'mostly_clear';
    if (code === 3) return 'cloudy';
    if (code === 45 || code === 48) return 'fog';
    if (code >= 51 && code <= 57) return 'drizzle';
    if ((code >= 61 && code <= 67) || (code >= 80 && code <= 82)) return 'rain';
    if ((code >= 71 && code <= 77) || code === 85 || code === 86) return 'snow';
    if (code >= 95) return 'thunder';
    return 'cloudy';
}

function icon(code, cls) {
    return `<svg viewBox="0 0 24 24" class="${cls}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[kind(code)]}</svg>`;
}

const sign = (n) => (n > 0 ? `+${n}` : `${n}`.replace('-', '−'));

function renderWeather(el, d, t, locale) {
    const wx = (code) => t.wx[kind(code)] || '';
    if (el.closest('[data-place]').hasAttribute('data-compact')) {
        const today = d.days[0];
        el.innerHTML = `<div class="flex items-center gap-5">
            ${icon(d.now.code, 'size-16 shrink-0 text-ember')}
            <div class="flex flex-col gap-1">
                <span class="font-display text-5xl leading-none text-forest">${sign(d.now.temp)}°</span>
                <span class="text-base text-slate">${wx(d.now.code)}${today ? ` · ${sign(today.min)}…${sign(today.max)}°` : ''}</span>
                <span class="text-[13px] text-stone">${t.feels} ${sign(d.now.feels)}° · ${t.wind} ${d.now.wind} ${t.ms}</span>
            </div>
        </div>`;
        const st = el.parentElement.querySelector('[data-weather-stamp]');
        if (st) st.textContent = t.note.replace(':alt', el.dataset.alt || '').replace(':time', d.updated);
        return;
    }
    const days = d.days.map((day, i) => {
        const name = i === 0 ? t.today : new Date(day.date + 'T12:00:00').toLocaleDateString(locale, { weekday: 'short' });
        return `<li class="flex flex-col items-center gap-1.5 rounded-2xl bg-snow px-2 py-3 text-center">
            <span class="text-[12px] uppercase tracking-[0.12em] text-stone">${name}</span>
            ${icon(day.code, 'size-7 text-ember')}
            <span class="text-base font-medium text-forest">${sign(day.max)}°</span>
            <span class="text-sm text-stone">${sign(day.min)}°</span>
            ${day.snow > 0 ? `<span class="text-[12px] text-forest-light">${t.snow} ${Math.round(day.snow)} ${t.cm}</span>` : ''}
        </li>`;
    }).join('');
    el.innerHTML = `
        <div class="flex flex-col gap-6 lg:flex-row lg:items-stretch lg:gap-10">
            <div class="flex items-center gap-5 lg:w-[340px] lg:shrink-0">
                ${icon(d.now.code, 'size-16 shrink-0 text-ember')}
                <div class="flex flex-col gap-1">
                    <span class="font-display text-5xl leading-none text-forest">${sign(d.now.temp)}°</span>
                    <span class="text-base text-slate">${wx(d.now.code)}</span>
                    <span class="text-[13px] text-stone">${t.feels} ${sign(d.now.feels)}° · ${t.wind} ${d.now.wind} ${t.ms}${d.now.gust > d.now.wind ? ` (${t.gust} ${d.now.gust})` : ''}</span>
                </div>
            </div>
            <ul class="grid flex-1 grid-cols-3 gap-2 sm:grid-cols-6">${days}</ul>
        </div>`;
    const stamp = el.parentElement.querySelector('[data-weather-stamp]');
    if (stamp) stamp.textContent = t.note.replace(':alt', el.dataset.alt || '').replace(':time', d.updated);
}

document.querySelectorAll('[data-weather]').forEach(async (el) => {
    const root = el.closest('[data-place]');
    const t = JSON.parse(root.dataset.i18n);
    const locale = root.dataset.locale;
    try {
        const res = await fetch(el.dataset.weather, { headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error('weather');
        renderWeather(el, await res.json(), t, locale);
    } catch (e) {
        el.innerHTML = `<p class="text-stone">${t.unavailable}</p>`;
    }
});

// Камеры: снимок обновляется сам, пока вкладка открыта.
document.querySelectorAll('[data-cam]').forEach((fig) => {
    const img = fig.querySelector('img');
    if (!img) return;
    const base = img.dataset.src;
    const every = Math.max(30, parseInt(fig.dataset.refresh || '120', 10)) * 1000;
    const stamp = fig.querySelector('[data-cam-stamp]');
    const err = fig.querySelector('[data-cam-error]');
    const load = () => { img.src = base + (base.includes('?') ? '&' : '?') + '_=' + Date.now(); };
    img.addEventListener('load', () => {
        err?.classList.add('hidden');
        if (stamp) stamp.textContent = new Date().toLocaleTimeString(document.documentElement.lang || undefined, { hour: '2-digit', minute: '2-digit' });
    });
    img.addEventListener('error', () => err?.classList.remove('hidden'));
    load();
    setInterval(() => { if (!document.hidden) load(); }, every);
});

document.querySelectorAll('[data-cam-embed]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const frame = document.createElement('iframe');
        frame.src = btn.dataset.camEmbed;
        frame.className = 'absolute inset-0 h-full w-full';
        frame.allow = 'autoplay; fullscreen';
        frame.loading = 'lazy';
        frame.referrerPolicy = 'no-referrer';
        btn.closest('[data-cam-box]').replaceChildren(frame);
    });
});

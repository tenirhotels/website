/*
 * Tenir Hotels — небольшой JS без фреймворков:
 * - мобильное меню;
 * - компактная шапка при прокрутке.
 */

// Мобильное меню: плавное открытие/закрытие, пункты появляются по очереди
const menu = document.getElementById('mobile-menu');
const toggles = document.querySelectorAll('[data-menu-toggle]');
menu?.querySelectorAll('nav a, nav + div, div > a').forEach((el, i) => el.style.setProperty('--i', String(Math.min(i, 12))));
const setMenu = (open) => {
    if (!menu) return;
    menu.classList.toggle('is-open', open);
    menu.toggleAttribute('inert', !open);
    menu.setAttribute('aria-hidden', String(!open));
    document.body.classList.toggle('overflow-hidden', open);
    toggles.forEach((b) => b.setAttribute('aria-expanded', String(open)));
};
toggles.forEach((btn) => btn.addEventListener('click', () => setMenu(!menu?.classList.contains('is-open'))));
menu?.addEventListener('click', (e) => { if (e.target.closest('a')) setMenu(false); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
window.matchMedia('(min-width: 1280px)').addEventListener('change', (e) => { if (e.matches) setMenu(false); });

// Плавное появление блоков при прокрутке (только если JS работает и анимации не отключены)
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const items = [...document.querySelectorAll('main > section:not(:first-child), main > div > section:not(:first-child), [data-reveal]')];
    const io = new IntersectionObserver((entries) => entries.forEach((en) => {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
    }), { rootMargin: '0px 0px -8% 0px', threshold: 0.04 });
    items.forEach((el) => {
        if (el.getBoundingClientRect().top < window.innerHeight) return; // уже на экране — без анимации
        el.classList.add('reveal');
        io.observe(el);
    });
}

// Шапка: прозрачная над героем, плотная при прокрутке
const header = document.querySelector('[data-header]');
if (header?.dataset.transparent === 'true') {
    const onScroll = () => header.classList.toggle('is-solid', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Запоминаем выбранный язык: корень сайта «/» дальше ведёт на него, а не на язык браузера.
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[hreflang][lang]');
    if (link) {
        document.cookie = 'tenir_lang=' + link.getAttribute('lang') + '; path=/; max-age=31536000; samesite=lax';
    }
});

// Лента карточек: тонкий индикатор прокрутки (как у Aman) — длина бегунка = видимая часть, положение = где вы в ленте
document.querySelectorAll('.rail').forEach((rail) => {
    const bar = document.createElement('div');
    bar.className = 'rail-progress';
    bar.setAttribute('aria-hidden', 'true');
    bar.innerHTML = '<span class="rail-progress__thumb"></span>';
    rail.after(bar);
    const thumb = bar.firstElementChild;

    const update = () => {
        const max = rail.scrollWidth - rail.clientWidth;
        bar.hidden = max <= 2;
        if (bar.hidden) return;
        const ratio = rail.clientWidth / rail.scrollWidth;
        thumb.style.width = (ratio * 100) + '%';
        thumb.style.transform = 'translateX(' + (rail.scrollLeft / max) * ((1 - ratio) / ratio) * 100 + '%)';
    };

    const seek = (e) => {
        const r = bar.getBoundingClientRect();
        const p = Math.min(1, Math.max(0, (e.clientX - r.left) / r.width));
        rail.scrollTo({ left: p * (rail.scrollWidth - rail.clientWidth), behavior: 'smooth' });
    };
    bar.addEventListener('click', seek);

    rail.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    new ResizeObserver(update).observe(rail);
    update();
});

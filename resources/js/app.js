/*
 * Tenir Hotels — небольшой JS без фреймворков:
 * - форма бронирования Exely: большая на первом экране → в шапке при прокрутке (как у Airbnb);
 * - мобильное меню (на десктопе открывается кнопкой в «сжатой» шапке);
 * - компактная шапка при прокрутке.
 */

// Мобильное меню
const menu = document.getElementById('mobile-menu');
document.querySelectorAll('[data-menu-toggle]').forEach((btn) =>
    btn.addEventListener('click', () => {
        const open = menu?.hasAttribute('hidden');
        if (!menu) return;
        menu.toggleAttribute('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
        document.querySelectorAll('[data-menu-toggle]').forEach((b) => b.setAttribute('aria-expanded', String(open)));
    }),
);

// Шапка: прозрачная над героем, плотная при прокрутке
const header = document.querySelector('[data-header]');
if (header?.dataset.transparent === 'true') {
    const onScroll = () => header.classList.toggle('is-solid', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Форма бронирования: один и тот же виджет Exely переезжает между первым экраном и шапкой.
(() => {
    const hero = document.querySelector('[data-search-slot="hero"]');
    const slot = document.querySelector('[data-search-slot="header"]');
    const box = document.getElementById('tl-search-form');
    if (!hero || !slot || !box || !header) return;

    const desktop = window.matchMedia('(min-width: 1024px)');
    let compact = null;

    const update = () => {
        const next = desktop.matches && hero.getBoundingClientRect().top < header.offsetHeight - 8;
        if (next === compact) return;
        compact = next;
        (next ? slot : hero).appendChild(box);
        box.dataset.state = next ? 'header' : 'hero';
        header.dataset.compact = String(next);
        if (!next) document.getElementById('mobile-menu')?.setAttribute('hidden', '');
    };

    window.addEventListener('scroll', update, { passive: true });
    desktop.addEventListener('change', update);
    update();
})();

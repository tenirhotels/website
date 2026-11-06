/*
 * Tenir Hotels — небольшой JS без фреймворков:
 * - мобильное меню;
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
        document.querySelectorAll('[data-menu-toggle]').forEach((b) => {
            b.setAttribute('aria-expanded', String(open));
            b.querySelector('[data-icon-open]')?.toggleAttribute('hidden', open);
            b.querySelector('[data-icon-close]')?.toggleAttribute('hidden', !open);
        });
    }),
);

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

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

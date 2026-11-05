/*
 * Tenir Hotels — небольшой JS без фреймворков:
 * - окно бронирования (<dialog id="booking-dialog">) из шапки и нижней панели на телефоне;
 * - проверка дат (выезд позже заезда, не в прошлом);
 * - мобильное меню;
 * - компактная шапка при прокрутке.
 */

const iso = (d) => d.toISOString().slice(0, 10);

function initDates(form) {
    const inField = form.querySelector('[name="check_in"]');
    const outField = form.querySelector('[name="check_out"]');
    if (!inField || !outField) return;

    const today = new Date();
    inField.min = iso(today);

    const sync = () => {
        if (!inField.value) return;
        const next = new Date(inField.value);
        next.setDate(next.getDate() + 1);
        outField.min = iso(next);
        if (!outField.value || outField.value <= inField.value) outField.value = iso(next);
    };
    inField.addEventListener('change', sync);
    sync();
}

document.querySelectorAll('[data-booking-form]').forEach(initDates);

// Окно бронирования
const dialog = document.getElementById('booking-dialog');
document.querySelectorAll('[data-open-booking]').forEach((btn) =>
    btn.addEventListener('click', (e) => {
        if (!dialog) return;
        e.preventDefault();
        dialog.showModal();
        dialog.querySelector('input, select')?.focus();
    }),
);
dialog?.addEventListener('click', (e) => {
    if (e.target === dialog) dialog.close();
});
document.querySelectorAll('[data-close-booking]').forEach((btn) => btn.addEventListener('click', () => dialog?.close()));

// Промокод — раскрывается по ссылке
document.querySelectorAll('[data-promo-toggle]').forEach((btn) =>
    btn.addEventListener('click', () => {
        const field = btn.closest('form')?.querySelector('[data-promo-field]');
        if (!field) return;
        field.hidden = false;
        btn.hidden = true;
        field.querySelector('input')?.focus();
    }),
);

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

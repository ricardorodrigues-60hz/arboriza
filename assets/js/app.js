document.addEventListener('DOMContentLoaded', function () {
    const confirms = document.querySelectorAll('[data-confirm]');
    confirms.forEach(function (item) {
        item.addEventListener('click', function (event) {
            if (!confirm(item.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const menu = document.querySelector('.account-menu');
    const toggle = document.querySelector('.account-toggle');

    if (!menu || !toggle) return;

    toggle.addEventListener('click', function (event) {
        event.stopPropagation();
        const isOpen = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function () {
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});

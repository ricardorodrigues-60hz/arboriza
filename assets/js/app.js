document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (item) {
        item.addEventListener('click', function (event) {
            if (!confirm(item.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    const menu = document.querySelector('.account-menu');
    const toggle = document.querySelector('.account-toggle');

    if (menu && toggle) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = menu.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function () {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    }

    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.querySelector('.menu-toggle');

    if (sidebar && menuToggle) {
        menuToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = sidebar.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (sidebar.classList.contains('open') && !sidebar.contains(event.target)) {
                sidebar.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (menu && toggle) {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
            if (sidebar && menuToggle) {
                sidebar.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
});

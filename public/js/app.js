// Vanilla JavaScript for the public website. No build step - edit and refresh.
(function () {
    'use strict';

    function init() {
        // Mobile menu toggle
        var toggle = document.querySelector('[data-menu-toggle]');
        var menu = document.getElementById('mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function () {
                var open = menu.hasAttribute('hidden');
                menu.toggleAttribute('hidden', !open);
                toggle.setAttribute('aria-expanded', String(open));
                document.body.style.overflow = open ? 'hidden' : '';
            });
            menu.addEventListener('click', function (e) {
                if (e.target.closest('a')) {
                    menu.setAttribute('hidden', '');
                    toggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            });
        }

        // Header shadow once the page is scrolled
        var header = document.querySelector('[data-header]');
        if (header) {
            var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        // Account menu: close when clicking outside or pressing Escape
        var acct = document.querySelector('[data-acct]');
        if (acct) {
            document.addEventListener('click', function (e) {
                if (acct.open && !acct.contains(e.target)) { acct.open = false; }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { acct.open = false; }
            });
        }

        // Show / hide password buttons
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-toggle-password]');
            if (!btn) { return; }
            var input = document.getElementById(btn.getAttribute('data-toggle-password'));
            if (!input) { return; }
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? btn.getAttribute('data-hide') : btn.getAttribute('data-show');
            btn.setAttribute('aria-pressed', String(show));
        });

        // Home page search: send the visitor to the chosen service's booking page
        var search = document.querySelector('[data-service-search]');
        if (search) {
            search.addEventListener('submit', function (e) {
                var select = search.querySelector('select[name="service"]');
                var city = search.querySelector('input[name="city"]');
                if (select && select.value) {
                    e.preventDefault();
                    var url = select.value + '#book';
                    if (city && city.value.trim()) {
                        url = select.value + '?city=' + encodeURIComponent(city.value.trim()) + '#book';
                    }
                    window.location.href = url;
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

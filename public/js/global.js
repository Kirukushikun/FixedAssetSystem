// ─────────────────────────────────────────────────────────────────────────────
// Global functions are declared first — before any DOM access that could
// throw — so inline onclick attributes can always resolve them.
// ─────────────────────────────────────────────────────────────────────────────

window.openMobileNav = function () {
    document.getElementById('main-nav').classList.add('mobile-open');
    document.getElementById('nav-backdrop').classList.add('active');
    document.body.style.overflow = 'hidden';
};

window.closeMobileNav = function () {
    document.getElementById('main-nav').classList.remove('mobile-open');
    document.getElementById('nav-backdrop').classList.remove('active');
    document.body.style.overflow = '';
};

window.toggleSidebar = function () {
    if (window.innerWidth <= 768) {
        window.openMobileNav();
        return;
    }
    var nav = document.getElementById('main-nav');
    var isNowCollapsed = nav.classList.toggle('collapsed');
    localStorage.setItem('nav-collapsed', isNowCollapsed);
};

// ─────────────────────────────────────────────────────────────────────────────
// DOM-dependent setup — runs after the page is fully parsed
// ─────────────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    // Restore desktop sidebar collapsed state
    var nav = document.getElementById('main-nav');
    if (localStorage.getItem('nav-collapsed') === 'true') {
        nav.classList.add('collapsed');
    }

    // Auto-close mobile nav when resizing to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            window.closeMobileNav();
        }
    });

    // Close mobile nav on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeMobileNav();
    });

    // ── Tab switching ─────────────────────────────────────────────────────────
    var tabs     = document.querySelectorAll('.tab-btn');
    var contents = document.querySelectorAll('.tab-content');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var target = tab.getAttribute('data-tab');

            // Reset all tab buttons
            tabs.forEach(function (t) {
                t.classList.remove('active', 'bg-white', 'text-gray-800', 'border', 'border-b-0', 'border-gray-200');
                t.classList.add('bg-gray-100', 'text-gray-600');
            });

            // Activate clicked tab
            tab.classList.add('active', 'bg-white', 'text-gray-800', 'border', 'border-b-0', 'border-gray-200');
            tab.classList.remove('bg-gray-100', 'text-gray-600');

            // Hide all content panels, show the target
            contents.forEach(function (c) { c.classList.add('hidden'); });
            document.getElementById(target).classList.remove('hidden');
        });
    });

});

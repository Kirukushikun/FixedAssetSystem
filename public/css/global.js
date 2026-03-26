// ============================================================
//  FIXED ASSET — global.js
// ============================================================

const nav      = document.getElementById('main-nav');
const backdrop = document.getElementById('nav-backdrop');

// Restore desktop collapsed state across page loads
let collapsed = localStorage.getItem('nav-collapsed') === 'true';
if (collapsed) nav.classList.add('collapsed');

function toggleSidebar() {
    if (window.innerWidth <= 768) {
        // On mobile the toggle-btn is hidden — this shouldn't
        // fire, but guard anyway
        openMobileNav();
        return;
    }
    collapsed = !collapsed;
    nav.classList.toggle('collapsed', collapsed);
    localStorage.setItem('nav-collapsed', collapsed);
}

function openMobileNav() {
    nav.classList.add('mobile-open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileNav() {
    nav.classList.remove('mobile-open');
    backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

// Close on resize back to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) closeMobileNav();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMobileNav();
});
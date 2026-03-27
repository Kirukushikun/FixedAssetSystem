const nav      = document.getElementById('main-nav');
const backdrop = document.getElementById('nav-backdrop');

let collapsed = localStorage.getItem('nav-collapsed') === 'true';
if (collapsed) nav.classList.add('collapsed');

window.toggleSidebar = function() {
    if (window.innerWidth <= 768) {
        window.openMobileNav();
        return;
    }
    collapsed = !collapsed;
    nav.classList.toggle('collapsed', collapsed);
    localStorage.setItem('nav-collapsed', collapsed);
}

window.openMobileNav = function() {
    nav.classList.add('mobile-open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}

window.closeMobileNav = function() {
    nav.classList.remove('mobile-open');
    backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

window.addEventListener('resize', () => {
    if (window.innerWidth > 768) window.closeMobileNav();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') window.closeMobileNav();
});
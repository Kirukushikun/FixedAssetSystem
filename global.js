const toggleBtn = document.getElementById('toggle-btn');
const nav = document.querySelector('nav');

toggleBtn.addEventListener('click', () => {
     nav.classList.toggle('collapsed');
});
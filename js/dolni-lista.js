document.addEventListener('DOMContentLoaded', function () {
// Najde všechny texty (span) v odkazech lišty
const navLinks = document.querySelectorAll('.bottom-nav-bar li a span');

    // Projde všechny nalezené texty a skryje je
    navLinks.forEach(span => {
        span.style.display = 'none';
    });
});
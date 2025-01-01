document.addEventListener('DOMContentLoaded', function () {
    const navButton = document.getElementById('nav-btn');
    const navLinks = document.getElementById('nav-links');

    // Toggle the 'active' class on the nav links
    navButton.addEventListener('click', function () {
        navLinks.classList.toggle('active');
    });
});
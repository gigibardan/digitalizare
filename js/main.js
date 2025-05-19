// js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Încărcarea header-ului
    const headerElement = document.querySelector('#header-placeholder');
    if (headerElement) {
        fetch('/includes/header.html')
            .then(response => response.text())
            .then(data => {
                headerElement.innerHTML = data;
                initNavigation(); // Inițializează funcționalitățile de navigare
            });
    }
    
    // Încărcarea footer-ului
    const footerElement = document.querySelector('#footer-placeholder');
    if (footerElement) {
        fetch('/includes/footer.html')
            .then(response => response.text())
            .then(data => {
                footerElement.innerHTML = data;
            });
    }
    
    // Funcții pentru navigarea responsivă
    function initNavigation() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navList = document.querySelector('.nav-list');
        const dropdowns = document.querySelectorAll('.dropdown');
        
        if (menuToggle && navList) {
            menuToggle.addEventListener('click', function() {
                navList.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
        
        if (dropdowns) {
            dropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('a');
                if (link) {
                    link.addEventListener('click', function(e) {
                        if (window.innerWidth < 992) {
                            e.preventDefault();
                            dropdown.classList.toggle('open');
                        }
                    });
                }
            });
        }
    }
});
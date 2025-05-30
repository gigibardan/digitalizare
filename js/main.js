// js/main.js
document.addEventListener('DOMContentLoaded', function () {
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
            menuToggle.addEventListener('click', function () {
                navList.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }

        if (dropdowns) {
            dropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('a');
                if (link) {
                    link.addEventListener('click', function (e) {
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

// Funcționalitate pentru acordeon
document.addEventListener('DOMContentLoaded', function () {
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const accordionId = this.getAttribute('data-accordion');
            const content = document.getElementById('accordion-' + accordionId);
            const icon = this.querySelector('.accordion-icon i');

            // Toggle pentru clasa active pe content
            if (content) {
                content.classList.toggle('active');
            }

            // Schimbăm iconița plus/minus
            if (icon.classList.contains('fa-plus')) {
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }

            // Închidem celelalte acordeoane
            accordionHeaders.forEach(otherHeader => {
                if (otherHeader !== header) {
                    const otherId = otherHeader.getAttribute('data-accordion');
                    const otherContent = document.getElementById('accordion-' + otherId);
                    const otherIcon = otherHeader.querySelector('.accordion-icon i');

                    if (otherContent && otherContent.classList.contains('active')) {
                        otherContent.classList.remove('active');
                        otherIcon.classList.remove('fa-minus');
                        otherIcon.classList.add('fa-plus');
                    }
                }
            });
        });
    });
});

// Tab functionality for subjects
document.addEventListener('DOMContentLoaded', function () {
    // Get all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');

    // Add click event listener to each tab button
    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Get the tab id from data attribute
            const tabId = this.getAttribute('data-tab');

            // Remove active class from all tab buttons and content
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Add active class to current tab button and content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Set the first tab as active by default if none is active
    if (!document.querySelector('.tab-button.active')) {
        const firstTabButton = document.querySelector('.tab-button');
        if (firstTabButton) {
            const firstTabId = firstTabButton.getAttribute('data-tab');
            firstTabButton.classList.add('active');
            document.getElementById(firstTabId).classList.add('active');
        }
    }
});


document.addEventListener('DOMContentLoaded', function () {
    // Verifică dacă există secțiunea #plan în pagina curentă
    const planSection = document.getElementById('plan');
    const planLinks = document.querySelectorAll('.plan-link');

    if (planSection && window.location.pathname === '/index.html') {
        // Dacă suntem pe homepage, actualizăm linkul să ducă direct la #plan
        planLinks.forEach(link => {
            link.href = '#plan';
        });
    }
    // Altfel, rămâne "/index.html#plan" și va redirecționa către homepage
});

// Adaugă acest cod la sfârșitul main.js
// Verifică autentificarea după ce header-ul este încărcat
function checkAuthStatus() {
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const adminLink = document.getElementById('admin-link'); // Link-ul vechi
    const adminDropdown = document.getElementById('admin-dropdown'); // Noul dropdown
    
    if (loginBtn && logoutBtn) {
        fetch('/check_auth.php?t=' + new Date().getTime())  // Adăugăm timestamp pentru a evita cache-ul
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    // Actualizăm butoanele de autentificare
                    loginBtn.style.display = 'none';
                    logoutBtn.style.display = 'inline-block';
                    
                    // Afișăm numele utilizatorului dacă avem un element pentru aceasta
                    const welcomeUser = document.getElementById('welcome-user');
                    const usernameSpan = document.getElementById('username');
                    if (welcomeUser && usernameSpan && data.username) {
                        welcomeUser.style.display = 'inline-block';
                        usernameSpan.textContent = data.username;
                    }
                    
                    // Verificăm dacă utilizatorul este admin
                    if (data.isAdmin || data.user_type === 'admin') {
                        // Afișăm noul dropdown admin dacă există
                        if (adminDropdown) {
                            adminDropdown.style.display = 'block';
                        }
                        // Păstrăm și link-ul vechi pentru compatibilitate
                        if (adminLink) {
                            adminLink.style.display = 'inline-block';
                        }
                    } else {
                        // Nu este admin - ascundem ambele variante
                        if (adminDropdown) {
                            adminDropdown.style.display = 'none';
                        }
                        if (adminLink) {
                            adminLink.style.display = 'none';
                        }
                    }
                } else {
                    // Afișăm butonul de login și ascundem celelalte elemente
                    loginBtn.style.display = 'inline-block';
                    logoutBtn.style.display = 'none';
                    
                    // Ascundem numele utilizatorului
                    const welcomeUser = document.getElementById('welcome-user');
                    if (welcomeUser) {
                        welcomeUser.style.display = 'none';
                    }
                    
                    // Ascundem toate link-urile de admin
                    if (adminDropdown) {
                        adminDropdown.style.display = 'none';
                    }
                    if (adminLink) {
                        adminLink.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Eroare la verificarea autentificării:', error));
    } else {
        // Încă nu există butonul, reîncercăm mai târziu
        setTimeout(checkAuthStatus, 300);
    }
}

// După ce pagina se încarcă complet
document.addEventListener('DOMContentLoaded', function() {
    // Încercăm să verificăm după ce header-ul a fost încărcat
    setTimeout(checkAuthStatus, 500);
    
    // Verificăm periodic starea autentificării (pentru a actualiza UI-ul dacă sesiunea expiră)
    setInterval(checkAuthStatus, 60000); // Verifică la fiecare minut
});

// Script pentru înlocuirea automată a numărului de telefon
function replacePhoneNumber() {
    const oldPhone = '+40 758 707 448';
    const newPhone = '+40 720 549 719';
    
    // Funcție pentru înlocuirea textului în toate nodurile text
    function replaceTextInNode(node) {
        if (node.nodeType === 3) { // Text node
            if (node.textContent.includes(oldPhone)) {
                node.textContent = node.textContent.replace(new RegExp(oldPhone.replace(/[+\s]/g, '\\$&'), 'g'), newPhone);
            }
        } else if (node.nodeType === 1) { // Element node
            // Parcurge toți copiii nodului
            for (let child of node.childNodes) {
                replaceTextInNode(child);
            }
        }
    }
    
    // Începe înlocuirea din body
    replaceTextInNode(document.body);
    
    // Înlocuiește și în atributele href pentru linkurile tel:
    const telLinks = document.querySelectorAll('a[href*="tel:+40758707448"], a[href*="tel:+40 758 707 448"], a[href*="tel:40758707448"]');
    telLinks.forEach(link => {
        link.href = link.href
            .replace('tel:+40758707448', 'tel:+40720549719')
            .replace('tel:+40 758 707 448', 'tel:+40 720 549 719')
            .replace('tel:40758707448', 'tel:40720549719');
    });
}

// Execută înlocuirea când pagina s-a încărcat complet
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', replacePhoneNumber);
} else {
    replacePhoneNumber();
}
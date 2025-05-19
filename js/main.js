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

// Funcționalitate pentru acordeon
document.addEventListener('DOMContentLoaded', function() {
  const accordionHeaders = document.querySelectorAll('.accordion-header');
  
  accordionHeaders.forEach(header => {
    header.addEventListener('click', function() {
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
document.addEventListener('DOMContentLoaded', function() {
            // Get all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            
            // Add click event listener to each tab button
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
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
            if(!document.querySelector('.tab-button.active')) {
                const firstTabButton = document.querySelector('.tab-button');
                const firstTabId = firstTabButton.getAttribute('data-tab');
                
                firstTabButton.classList.add('active');
                document.getElementById(firstTabId).classList.add('active');
            }
        });
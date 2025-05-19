// js/modules.js
document.addEventListener('DOMContentLoaded', function() {
    // Inițializare hotspots interactivi
    initHotspots();
    
    // Salvare progres modul în localStorage
    trackModuleProgress();
    
    // Funcționalități pentru exerciții interactive
    initExercises();
});

// Funcție pentru inițializarea punctelor interactive (hotspots)
function initHotspots() {
    const hotspots = document.querySelectorAll('.hotspot');
    if (!hotspots.length) return;
    
    hotspots.forEach(hotspot => {
        hotspot.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const allContents = document.querySelectorAll('.hotspot-content');
            
            // Ascunde toate conținuturile anterioare
            allContents.forEach(content => {
                content.style.opacity = '0';
                content.style.transform = 'translateY(100%)';
            });
            
            // Afișează conținutul dorit
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.style.opacity = '1';
                targetContent.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Afișează primul hotspot automat după încărcare
    setTimeout(() => {
        if (hotspots[0]) {
            hotspots[0].click();
        }
    }, 1000);
}

// Funcție pentru urmărirea progresului în modul
function trackModuleProgress() {
    // Verifică dacă suntem pe o pagină de lecție
    const breadcrumbs = document.querySelector('.breadcrumbs');
    if (!breadcrumbs) return;
    
    // Extrage informații despre modul și lecție din breadcrumbs
    const breadcrumbText = breadcrumbs.textContent;
    let moduleMatch = breadcrumbText.match(/Modul (\d+)/);
    let lessonMatch = breadcrumbText.match(/Lecția (\d+)/);
    
    if (moduleMatch && lessonMatch) {
        const moduleNumber = moduleMatch[1];
        const lessonNumber = lessonMatch[1];
        const moduleKey = `module_${moduleNumber}_progress`;
        
        // Salvează progresul în localStorage
        let moduleProgress = JSON.parse(localStorage.getItem(moduleKey) || '{}');
        moduleProgress[`lesson_${lessonNumber}`] = true;
        localStorage.setItem(moduleKey, JSON.stringify(moduleProgress));
        
        // Actualizează UI pentru a reflecta progresul
        updateProgressUI(moduleNumber, moduleProgress);
    }
}

// Actualizează interfața pentru a reflecta progresul
function updateProgressUI(moduleNumber, progressData) {
    const moduleNav = document.querySelector('.lesson-nav-sidebar ul');
    if (!moduleNav) return;
    
    const lessonItems = moduleNav.querySelectorAll('li');
    let completedCount = 0;
    
    lessonItems.forEach((item, index) => {
        const lessonNum = index + 1;
        
        if (progressData[`lesson_${lessonNum}`]) {
            completedCount++;
            
            // Adaugă indicator de completare dacă nu e lecția curentă
            if (!item.classList.contains('active')) {
                let checkMark = document.createElement('span');
                checkMark.className = 'lesson-completed';
                checkMark.innerHTML = '<i class="fas fa-check-circle"></i>';
                item.appendChild(checkMark);
            }
        }
    });
    
    // Actualizează bara de progres
    const progressBar = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text span:last-child');
    
    if (progressBar && progressText) {
        const totalLessons = lessonItems.length;
        const percentComplete = (completedCount / totalLessons) * 100;
        
        progressBar.style.width = `${percentComplete}%`;
        progressText.textContent = `${completedCount}/${totalLessons}`;
    }
}

// Funcționalități pentru exercițiile interactive
function initExercises() {
    const exerciseBoxes = document.querySelectorAll('.exercise-box');
    if (!exerciseBoxes.length) return;
    
    exerciseBoxes.forEach(box => {
        // Adaugă funcționalitate pentru butonul "Verifică"
        const checkButton = box.querySelector('.check-answer-btn');
        if (checkButton) {
            checkButton.addEventListener('click', function() {
                checkExerciseAnswer(this.closest('.exercise-box'));
            });
        }
        
        // Adaugă funcționalitate pentru butonul "Resetează"
        const resetButton = box.querySelector('.reset-exercise-btn');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                resetExercise(this.closest('.exercise-box'));
            });
        }
    });
}

// Verifică răspunsul la exercițiu
function checkExerciseAnswer(exerciseBox) {
    // Implementarea specifică pentru verificarea răspunsurilor
    // va fi dezvoltată în funcție de tipurile de exerciții
    console.log('Checking exercise answer...');
    
    // Exemplu pentru exercițiu cu răspunsuri multiple
    const options = exerciseBox.querySelectorAll('.option-item input[type="checkbox"]');
    let isCorrect = true;
    
    options.forEach(option => {
        const shouldBeChecked = option.getAttribute('data-correct') === 'true';
        if (option.checked !== shouldBeChecked) {
            isCorrect = false;
        }
    });
    
    // Afișează feedback
    const feedbackElement = exerciseBox.querySelector('.exercise-feedback');
    if (feedbackElement) {
        feedbackElement.textContent = isCorrect 
            ? 'Felicitări! Răspuns corect!' 
            : 'Răspuns incorect. Încearcă din nou!';
        feedbackElement.className = 'exercise-feedback ' + (isCorrect ? 'correct' : 'incorrect');
        feedbackElement.style.display = 'block';
    }
}

// Resetează exercițiul la starea inițială
function resetExercise(exerciseBox) {
    // Resetează toate câmpurile de input
    exerciseBox.querySelectorAll('input').forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    
    // Resetează selecturile
    exerciseBox.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });
    
    // Ascunde feedback-ul
    const feedbackElement = exerciseBox.querySelector('.exercise-feedback');
    if (feedbackElement) {
        feedbackElement.style.display = 'none';
    }
}
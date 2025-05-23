// js/evaluation.js

class EvaluationSystem {
    constructor() {
        this.currentQuestion = 0;
        this.questions = [];
        this.answers = {};
        this.studentData = {};
        this.startTime = null;
        this.timerInterval = null;
        this.testId = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadQuestions();
    }
    
    bindEvents() {
        // Formular identificare
        const studentForm = document.getElementById('student-form');
        if (studentForm) {
            studentForm.addEventListener('submit', (e) => this.handleStudentForm(e));
        }
        
        // Navigare test
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const finishBtn = document.getElementById('finish-btn');
        
        if (prevBtn) prevBtn.addEventListener('click', () => this.previousQuestion());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextQuestion());
        if (finishBtn) finishBtn.addEventListener('click', () => this.finishTest());
    }
    
    async loadQuestions() {
        try {
            const response = await fetch('/resources/evaluation_api.php?action=get_questions');
            const data = await response.json();
            
            if (data.success) {
                this.questions = data.questions;
            } else {
                this.showToast('Eroare la încărcarea întrebărilor', 'error');
            }
        } catch (error) {
            console.error('Error loading questions:', error);
            this.showToast('Eroare de conexiune', 'error');
        }
    }
    
    handleStudentForm(e) {
        e.preventDefault();
        
        // Validare formular
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        if (!this.validateStudentData(data)) {
            return;
        }
        
        this.studentData = data;
        this.startTest();
    }
    
    validateStudentData(data) {
        let isValid = true;
        
        // Resetează erorile
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
        });
        
        // Validare nume
        if (!data.nume || data.nume.trim().length < 2) {
            this.showFieldError('nume', 'Numele trebuie să aibă cel puțin 2 caractere');
            isValid = false;
        }
        
        // Validare prenume
        if (!data.prenume || data.prenume.trim().length < 2) {
            this.showFieldError('prenume', 'Prenumele trebuie să aibă cel puțin 2 caractere');
            isValid = false;
        }
        
        // Validare CNP
        if (!data.cnp || !this.validateCNP(data.cnp)) {
            this.showFieldError('cnp', 'CNP-ul trebuie să conțină exact 13 cifre');
            isValid = false;
        }
        
        // Validare instituția
        if (!data.institutia || data.institutia.trim().length < 3) {
            this.showFieldError('institutia', 'Numele instituției trebuie să aibă cel puțin 3 caractere');
            isValid = false;
        }
        
        return isValid;
    }
    
    validateCNP(cnp) {
        return /^[0-9]{13}$/.test(cnp);
    }
    
    showFieldError(fieldName, message) {
        const errorEl = document.getElementById(`${fieldName}-error`);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('show');
        }
    }
    
    async startTest() {
        try {
            // Creează sesiunea de test
            const response = await fetch('/resources/evaluation_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'start_test',
                    student_data: this.studentData
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.testId = data.test_id;
                this.showTestContainer();
                this.displayQuestion();
                this.startTimer();
            } else {
                this.showToast(data.message || 'Eroare la începerea testului', 'error');
            }
        } catch (error) {
            console.error('Error starting test:', error);
            this.showToast('Eroare de conexiune', 'error');
        }
    }
    
    showTestContainer() {
        document.getElementById('identification-form').style.display = 'none';
        document.getElementById('test-container').style.display = 'block';
        
        // Scroll la test
        document.getElementById('test-container').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
    
    displayQuestion() {
        if (!this.questions || this.questions.length === 0) {
            this.showToast('Nu s-au găsit întrebări', 'error');
            return;
        }
        
        const question = this.questions[this.currentQuestion];
        const container = document.getElementById('question-container');
        
        container.innerHTML = `
            <div class="question-card">
                <div class="question-number">
                    Întrebarea ${this.currentQuestion + 1} din ${this.questions.length} 
                    - Modul ${question.modul} (${question.dificultate})
                </div>
                <div class="question-text">${question.intrebarea}</div>
                <div class="question-options">
                    <div class="option-item" data-answer="a">
                        <input type="radio" name="q${question.id}" value="a" class="option-radio" id="option-a-${question.id}">
                        <label class="option-label" for="option-a-${question.id}">A)</label>
                        <span class="option-text">${question.varianta_a}</span>
                    </div>
                    <div class="option-item" data-answer="b">
                        <input type="radio" name="q${question.id}" value="b" class="option-radio" id="option-b-${question.id}">
                        <label class="option-label" for="option-b-${question.id}">B)</label>
                        <span class="option-text">${question.varianta_b}</span>
                    </div>
                    <div class="option-item" data-answer="c">
                        <input type="radio" name="q${question.id}" value="c" class="option-radio" id="option-c-${question.id}">
                        <label class="option-label" for="option-c-${question.id}">C)</label>
                        <span class="option-text">${question.varianta_c}</span>
                    </div>
                </div>
            </div>
        `;
        
        // Bind evenimente pentru opțiuni
        container.querySelectorAll('.option-item').forEach(option => {
            option.addEventListener('click', () => {
                const radio = option.querySelector('input[type="radio"]');
                radio.checked = true;
                this.saveAnswer(question.id, radio.value);
                
                // Update visual selection
                container.querySelectorAll('.option-item').forEach(opt => {
                    opt.classList.remove('selected');
                });
                option.classList.add('selected');
            });
        });
        
        // Restaurează răspunsul salvat
        if (this.answers[question.id]) {
            const savedAnswer = this.answers[question.id];
            const radio = container.querySelector(`input[value="${savedAnswer}"]`);
            if (radio) {
                radio.checked = true;
                radio.closest('.option-item').classList.add('selected');
            }
        }
        
        this.updateProgress();
        this.updateNavigation();
    }
    
    saveAnswer(questionId, answer) {
        this.answers[questionId] = answer;
        
        // Auto-save la server
        this.autoSave();
    }
    
    async autoSave() {
        if (!this.testId) return;
        
        try {
            await fetch('/resources/evaluation_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'save_progress',
                    test_id: this.testId,
                    answers: this.answers
                })
            });
        } catch (error) {
            console.error('Auto-save error:', error);
        }
    }
    
    updateProgress() {
        const progress = ((this.currentQuestion + 1) / this.questions.length) * 100;
        document.getElementById('progress-fill').style.width = `${progress}%`;
        document.getElementById('question-counter').textContent = 
            `Întrebarea ${this.currentQuestion + 1} din ${this.questions.length}`;
    }
    
    updateNavigation() {
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const finishBtn = document.getElementById('finish-btn');
        
        // Buton înapoi
        prevBtn.disabled = this.currentQuestion === 0;
        
        // Buton următoarea / finalizează
        if (this.currentQuestion === this.questions.length - 1) {
            nextBtn.style.display = 'none';
            finishBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            finishBtn.style.display = 'none';
        }
    }
    
    previousQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.displayQuestion();
        }
    }
    
    nextQuestion() {
        if (this.currentQuestion < this.questions.length - 1) {
            this.currentQuestion++;
            this.displayQuestion();
        }
    }
    
    async finishTest() {
        // Verifică dacă toate întrebările au răspuns
        const unanswered = this.questions.filter(q => !this.answers[q.id]);
        
        if (unanswered.length > 0) {
            const proceed = confirm(
                `Aveți ${unanswered.length} întrebări fără răspuns. ` +
                `Doriți să finalizați testul oricum?`
            );
            if (!proceed) return;
        }
        
        try {
            const response = await fetch('/resources/evaluation_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'finish_test',
                    test_id: this.testId,
                    answers: this.answers
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.stopTimer();
                this.showResults(data.results);
            } else {
                this.showToast(data.message || 'Eroare la finalizarea testului', 'error');
            }
        } catch (error) {
            console.error('Error finishing test:', error);
            this.showToast('Eroare de conexiune', 'error');
        }
    }
    
    showResults(results) {
        document.getElementById('test-container').style.display = 'none';
        document.getElementById('results-container').style.display = 'block';
        
        // Scroll la rezultate
        document.getElementById('results-container').scrollIntoView({ 
            behavior: 'smooth' 
        });
        
        const isPassed = results.scor >= 16;
        const percentage = Math.round((results.scor / 20) * 100);
        
        // Header rezultate
        const resultsHeader = document.getElementById('results-header');
        resultsHeader.innerHTML = `
            <div class="results-score">
                <div class="score-circle ${isPassed ? 'success' : 'fail'}">
                    ${results.scor}/20
                </div>
                <div class="score-text">Scorul dumneavoastră: ${results.scor} din 20 (${percentage}%)</div>
                <div class="score-message">
                    ${isPassed ? 
                        'Felicitări! Ați promovat testul!' : 
                        'Nu ați atins scorul minim de 16 puncte.'}
                </div>
            </div>
        `;
        
        // Conținut rezultate
        const resultsContent = document.getElementById('results-content');
        let contentHTML = '';
        
        if (isPassed) {
            contentHTML += `
                <div class="success-section">
                    <h3><i class="fas fa-trophy"></i> Felicitări!</h3>
                    <p>Ați absolvit cu succes cursul de digitalizare și utilizarea tablei interactive în educație.</p>
                    <p>Diploma dumneavoastră va fi procesată în scurt timp.</p>
                </div>
            `;
        } else {
            contentHTML += `
                <div class="retake-section">
                    <h3><i class="fas fa-redo"></i> Puteți relua testul</h3>
                    <p>Pentru a obține diploma, aveți nevoie de minimum 16 puncte din 20.</p>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-redo"></i> Reluați testul
                    </button>
                </div>
            `;
        }
        
        // Afișează răspunsurile greșite
        if (results.wrong_answers && results.wrong_answers.length > 0) {
            contentHTML += `
                <div class="wrong-answers">
                    <h3><i class="fas fa-times-circle"></i> Răspunsuri incorecte (${results.wrong_answers.length})</h3>
            `;
            
            results.wrong_answers.forEach(item => {
                contentHTML += `
                    <div class="wrong-answer-item">
                        <div class="wrong-question">
                            <strong>Întrebarea ${item.question_number}:</strong> ${item.intrebarea}
                        </div>
                        <div class="answer-comparison">
                            <div class="your-answer">
                                <div class="answer-label">Răspunsul dumneavoastră:</div>
                                <div>${item.your_answer_text}</div>
                            </div>
                            <div class="correct-answer">
                                <div class="answer-label">Răspunsul corect:</div>
                                <div>${item.correct_answer_text}</div>
                            </div>
                        </div>
                        ${item.explicatie ? `
                            <div class="explanation">
                                <h5>Explicație:</h5>
                                <p>${item.explicatie}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            contentHTML += `</div>`;
        }
        
        resultsContent.innerHTML = contentHTML;
    }
    
    startTimer() {
        this.startTime = new Date();
        this.updateTimer();
        
        this.timerInterval = setInterval(() => {
            this.updateTimer();
        }, 1000);
    }
    
    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }
    
    updateTimer() {
        if (!this.startTime) return;
        
        const now = new Date();
        const elapsed = Math.floor((now - this.startTime) / 1000);
        
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        
        const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('timer-display').textContent = display;
    }
    
    showToast(message, type = 'info') {
        // Șterge toast-urile existente
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Afișează toast-ul
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Șterge toast-ul după 5 secunde
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }
}

// FUNCȚIONALITATEA ACORDEONULUI - O SINGURĂ IMPLEMENTARE
function initAccordion() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const accordionId = this.getAttribute('data-accordion');
            const content = document.getElementById(`accordion-${accordionId}`);
            const icon = this.querySelector('.accordion-icon i');
            
            // Verifică dacă acordeonul este deja activ
            const isActive = content.classList.contains('active');
            
            // Închide toate acordeoanele
            accordionHeaders.forEach(otherHeader => {
                const otherId = otherHeader.getAttribute('data-accordion');
                const otherContent = document.getElementById(`accordion-${otherId}`);
                const otherIcon = otherHeader.querySelector('.accordion-icon i');
                
                otherContent.classList.remove('active');
                otherHeader.classList.remove('active');
                otherIcon.classList.remove('fa-minus');
                otherIcon.classList.add('fa-plus');
            });
            
            // Dacă acordeonul nu era activ, îl deschidem
            if (!isActive) {
                content.classList.add('active');
                this.classList.add('active');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        });
    });
}

// INIȚIALIZARE UNICĂ CÂND PAGINA SE ÎNCARCĂ
document.addEventListener('DOMContentLoaded', function() {
    // Inițializează sistemul de evaluare
    new EvaluationSystem();
    
    // Inițializează acordeonul cu mică întârziere
    setTimeout(initAccordion, 200);
});
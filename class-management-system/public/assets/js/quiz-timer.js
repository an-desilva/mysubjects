// Quiz Timer & Auto-Submission Handler
document.addEventListener('DOMContentLoaded', () => {
    if (!window.QUIZ_CONFIG) return;

    const { attemptId, durationMinutes, startedAt } = window.QUIZ_CONFIG;
    const timerDisplay = document.getElementById('timerDisplay');
    const progressBar = document.getElementById('timerProgressBar');
    const quizForm = document.getElementById('quizForm');
    const submitBtn = document.getElementById('submitBtn');

    if (!timerDisplay || !quizForm) return;

    const totalSeconds = durationMinutes * 60;
    
    // Compute remaining time based on startedAt or fallback
    const startTimeMs = new Date(startedAt.replace(/-/g, '/')).getTime();
    const nowMs = new Date().getTime();
    const elapsedSeconds = Math.max(0, Math.floor((nowMs - startTimeMs) / 1000));
    
    let remainingSeconds = Math.max(0, totalSeconds - elapsedSeconds);

    function updateDisplay() {
        const mins = Math.floor(remainingSeconds / 60);
        const secs = remainingSeconds % 60;
        const formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        timerDisplay.textContent = formatted;

        // Visual progress bar percentage calculation
        const percentLeft = Math.max(0, (remainingSeconds / totalSeconds) * 100);
        if (progressBar) {
            progressBar.style.width = `${percentLeft}%`;
            if (percentLeft < 20) {
                timerDisplay.classList.remove('text-amber-400');
                timerDisplay.classList.add('text-rose-500', 'animate-bounce');
            }
        }
    }

    updateDisplay();

    const timerInterval = setInterval(() => {
        remainingSeconds--;
        updateDisplay();

        if (remainingSeconds === 60) {
            // 1 Minute Warning
            alert('⚠️ Warning: 1 Minute Remaining! Please finalize your answers.');
        }

        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = '00:00';
            
            // Auto submit
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Time Expired! Submitting...';
            }
            alert('⏰ Time is up! Your quiz is being automatically submitted now.');
            quizForm.submit();
        }
    }, 1000);

    // Prevent accidental page leave without submit confirmation
    let isSubmitting = false;
    quizForm.addEventListener('submit', () => {
        isSubmitting = true;
        clearInterval(timerInterval);
    });

    window.addEventListener('beforeunload', (e) => {
        if (!isSubmitting && remainingSeconds > 0) {
            e.preventDefault();
            e.returnValue = 'You have an active quiz attempt in progress. Are you sure you want to leave?';
        }
    });
});

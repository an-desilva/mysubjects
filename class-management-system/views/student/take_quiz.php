<?php
$title = "Taking Quiz: " . htmlspecialchars($quiz['title']) . " - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
?>

<main class="w-full flex-1 p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto space-y-6">
    <!-- Sticky Countdown Timer Header Bar -->
    <div class="sticky top-16 z-30 glass-panel p-4 sm:p-5 rounded-2xl border border-slate-800 bg-slate-900/90 shadow-2xl flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-rose-400"><?= htmlspecialchars($quiz['course_title']) ?></span>
            <h1 class="text-lg sm:text-xl font-extrabold font-heading text-white"><?= htmlspecialchars($quiz['title']) ?></h1>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Time Remaining</span>
                <span id="timerDisplay" class="font-mono text-xl sm:text-2xl font-extrabold text-amber-400">00:00</span>
            </div>
            <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg border border-amber-500/20">
                <i class="fa-solid fa-stopwatch animate-pulse"></i>
            </div>
        </div>
    </div>

    <!-- Timer Visual Progress Bar -->
    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-800">
        <div id="timerProgressBar" class="bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-500 h-2 w-full transition-all duration-1000"></div>
    </div>

    <!-- Quiz Form -->
    <form id="quizForm" action="<?= base_url('/student/submit-quiz') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">

        <?php foreach ($quiz['questions'] as $idx => $q): ?>
            <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-4 shadow-xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800/80 pb-3">
                    <h3 class="font-heading font-bold text-base sm:text-lg text-white">
                        <span class="text-brand-400 mr-2">Q<?= $idx + 1 ?>.</span><?= htmlspecialchars($q['question_text']) ?>
                    </h3>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-xs font-mono text-slate-300 shrink-0">
                        <?= $q['marks'] ?> mark
                    </span>
                </div>

                <div class="space-y-2.5 pt-1">
                    <?php foreach ($q['options'] as $opt): ?>
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-900 cursor-pointer transition-all">
                            <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" class="h-4 w-4 text-brand-500 focus:ring-brand-500">
                            <span class="text-sm text-slate-200"><?= htmlspecialchars($opt['option_text']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Submit Bar -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex items-center justify-between shadow-2xl">
            <p class="text-xs text-slate-400">Make sure all questions are answered before submitting.</p>
            <button type="submit" id="submitBtn" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Submit Answers Now
            </button>
        </div>
    </form>
</main>

<script>
    window.QUIZ_CONFIG = {
        attemptId: <?= $attempt['id'] ?>,
        durationMinutes: <?= $quiz['duration_minutes'] ?>,
        startedAt: "<?= $attempt['started_at'] ?>"
    };
</script>
<script src="<?= asset('js/quiz-timer.js') ?>"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

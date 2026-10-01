<?php
$title = "Available Quizzes - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800">
        <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Online Quizzes & Assessments</h1>
        <p class="text-sm text-slate-400 mt-1">Take timed online quizzes for your enrolled tuition classes with automatic instant grading.</p>
    </div>

    <!-- Quiz Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($quizzes)): ?>
            <div class="col-span-full glass-panel p-12 text-center rounded-3xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-clipboard-question text-4xl text-slate-600"></i>
                <h3 class="text-lg font-bold text-slate-300">No Active Quizzes</h3>
                <p class="text-xs text-slate-500">There are currently no active quizzes assigned to your classes.</p>
            </div>
        <?php else: foreach ($quizzes as $q): ?>
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4 hover:border-amber-500/40 transition-all shadow-lg">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full bg-slate-900 border border-slate-700 text-indigo-300 text-xs font-semibold">
                            <?= htmlspecialchars($q['course_title']) ?>
                        </span>
                        <span class="text-xs font-mono text-amber-400 font-bold">
                            <i class="fa-regular fa-clock mr-1"></i><?= $q['duration_minutes'] ?> mins
                        </span>
                    </div>

                    <div>
                        <h3 class="font-heading font-bold text-lg text-white leading-snug"><?= htmlspecialchars($q['title']) ?></h3>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-2"><?= htmlspecialchars($q['description'] ?? 'No description') ?></p>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-300 pt-2">
                        <span><i class="fa-solid fa-list text-slate-500 mr-1"></i><?= $q['total_questions'] ?> Question(s)</span>
                        <span><i class="fa-solid fa-bullseye text-slate-500 mr-1"></i>Pass: <?= $q['pass_mark'] ?>%</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <?php if ($q['last_score'] !== null): ?>
                            <span class="text-xs text-slate-400">Previous Score: <strong class="<?= $q['last_score'] >= $q['pass_mark'] ? 'text-emerald-400' : 'text-rose-400' ?>"><?= number_format($q['last_score'], 1) ?>%</strong></span>
                        <?php else: ?>
                            <span class="text-xs text-amber-400 font-semibold">Not Attempted Yet</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= base_url('/student/take-quiz?quiz_id=' . $q['id']) ?>"
                       class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-500 hover:to-rose-500 text-white font-bold text-xs transition-all shadow-md flex items-center gap-1.5">
                        <i class="fa-solid fa-play"></i> <?= $q['last_score'] !== null ? 'Retake Quiz' : 'Start Quiz' ?>
                    </a>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

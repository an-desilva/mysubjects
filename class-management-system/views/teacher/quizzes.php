<?php
$title = "Quizzes & MCQ Creator - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-panel p-6 rounded-3xl border border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Quizzes & Online MCQ Exams</h1>
            <p class="text-sm text-slate-400 mt-1">Design timed online quizzes with automated countdown timers and instant grading.</p>
        </div>
        <button onclick="openModal('createQuizModal')" class="px-5 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold text-sm shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Create New Quiz
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quizzes List (1 Column or 2 Column if quiz selected) -->
        <div class="<?= $selectedQuiz ? 'lg:col-span-1' : 'lg:col-span-3' ?> space-y-4">
            <h2 class="font-heading font-bold text-lg text-white">Active Quizzes</h2>
            
            <div class="space-y-4">
                <?php if (empty($quizzes)): ?>
                    <div class="glass-panel p-8 text-center rounded-3xl border border-slate-800 text-slate-500">
                        No quizzes created yet. Click above to build your first quiz.
                    </div>
                <?php else: foreach ($quizzes as $q): ?>
                    <div class="glass-panel p-5 rounded-2xl border <?= ($selectedQuiz && $selectedQuiz['id'] == $q['id']) ? 'border-rose-500 bg-rose-950/20' : 'border-slate-800 hover:border-slate-700' ?> transition-all space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-indigo-300"><?= htmlspecialchars($q['course_title']) ?></span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-700 text-slate-300 text-xs font-mono">
                                <i class="fa-regular fa-clock mr-1"></i><?= $q['duration_minutes'] ?> min
                            </span>
                        </div>
                        <h3 class="font-heading font-bold text-base text-white"><?= htmlspecialchars($q['title']) ?></h3>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span><?= $q['total_questions'] ?> question(s)</span>
                            <a href="<?= base_url('/teacher/quizzes?id=' . $q['id']) ?>"
                               class="px-3 py-1 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-200 hover:text-white font-bold transition-all border border-slate-700">
                                Manage Questions →
                            </a>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Selected Quiz Questions Editor (2 Columns when selected) -->
        <?php if ($selectedQuiz): ?>
            <div class="lg:col-span-2 glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-rose-400 tracking-wider">Question Manager</span>
                        <h2 class="text-2xl font-extrabold font-heading text-white"><?= htmlspecialchars($selectedQuiz['title']) ?></h2>
                    </div>
                    <a href="<?= base_url('/teacher/quizzes') ?>" class="text-xs text-slate-400 hover:text-white">Close Editor ✕</a>
                </div>

                <!-- Existing Questions List -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-300 uppercase">Existing Questions (<?= count($selectedQuiz['questions']) ?>)</h3>
                    <?php if (empty($selectedQuiz['questions'])): ?>
                        <p class="text-xs text-slate-500 italic bg-slate-950 p-4 rounded-xl border border-slate-800 text-center">No questions added yet. Fill out the form below to add MCQ options.</p>
                    <?php else: foreach ($selectedQuiz['questions'] as $idx => $qItem): ?>
                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-white">Q<?= $idx + 1 ?>: <?= htmlspecialchars($qItem['question_text']) ?></span>
                                <span class="text-xs font-mono text-rose-400 font-bold"><?= $qItem['marks'] ?> mark(s)</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <?php foreach ($qItem['options'] as $opt): ?>
                                    <div class="p-2 rounded-lg <?= !empty($opt['is_correct']) ? 'bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 font-semibold' : 'bg-slate-900 border border-slate-800 text-slate-400' ?>">
                                        <?= !empty($opt['is_correct']) ? '✓ ' : '' ?><?= htmlspecialchars($opt['option_text']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

                <!-- Add New Question Form -->
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 space-y-4">
                    <h3 class="text-sm font-bold text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-plus text-rose-400"></i> Add MCQ Question
                    </h3>
                    <form action="<?= base_url('/teacher/quizzes/add-question') ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="quiz_id" value="<?= $selectedQuiz['id'] ?>">

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Question Statement *</label>
                                <input type="text" name="question_text" required placeholder="e.g. What is the derivative of sin(x)?"
                                       class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-rose-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Marks</label>
                                <input type="number" name="marks" value="1" min="1" required
                                       class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3 py-2 text-sm text-slate-100 text-center font-bold">
                            </div>
                        </div>

                        <!-- MCQ Options (Radio for correct) -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase">MCQ Options & Select Correct Answer *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php for ($i = 0; $i < 4; $i++): ?>
                                    <div class="flex items-center gap-2 bg-slate-950 p-2 rounded-xl border border-slate-800">
                                        <input type="radio" name="correct_option" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> title="Select as correct answer" class="text-rose-500">
                                        <input type="text" name="options[]" placeholder="Option <?= chr(65 + $i) ?>" required
                                               class="w-full bg-transparent text-xs text-slate-100 placeholder-slate-600 focus:outline-none">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm shadow-md transition-all">
                            Save Question to Quiz
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal: Create Quiz -->
<div id="createQuizModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-lg glass-panel rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-xl font-bold font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-stopwatch text-rose-400"></i> Create Online Quiz
            </h3>
            <button onclick="closeModal('createQuizModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('/teacher/quizzes/create') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Quiz Title *</label>
                <input type="text" name="title" required placeholder="e.g. Physics Mechanics Chapter Check"
                       class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-rose-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Target Class *</label>
                <select name="course_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-rose-500 focus:outline-none">
                    <option value="">-- Choose Class --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Duration (Minutes) *</label>
                    <input type="number" name="duration_minutes" value="15" min="1" max="180" required
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-rose-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pass Mark (%) *</label>
                    <input type="number" name="pass_mark" value="50" min="1" max="100" required
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-rose-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Description / Instructions</label>
                <textarea name="description" rows="2" placeholder="Instructions for students taking this quiz..."
                          class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('createQuizModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm shadow-lg shadow-rose-500/20">
                    Create & Proceed to Questions
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

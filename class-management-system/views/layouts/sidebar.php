<?php
$user = auth_user();
$role = $user['role'] ?? 'guest';
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

function is_active(string $path, string $currentUri): string {
    return str_contains($currentUri, $path) 
        ? 'bg-brand-600/20 text-indigo-300 font-semibold border-l-4 border-brand-500 shadow-sm' 
        : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200';
}
?>
<aside class="w-64 glass-panel border-r border-slate-800 bg-slate-900/60 hidden md:flex flex-col justify-between shrink-0 p-4">
    <div class="space-y-6">
        <div class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-slate-500">
            <?= strtoupper($role) ?> MENU
        </div>
        <nav class="space-y-1.5">
            <?php if ($role === 'admin'): ?>
                <a href="<?= base_url('/admin/dashboard') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/admin/dashboard', $currentUri) ?>">
                    <i class="fa-solid fa-chart-line text-base text-brand-400 w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('/admin/students') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/admin/students', $currentUri) ?>">
                    <i class="fa-solid fa-user-graduate text-base text-cyan-400 w-5"></i>
                    <span>Student Manager</span>
                </a>
                <a href="<?= base_url('/admin/attendance') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/admin/attendance', $currentUri) ?>">
                    <i class="fa-solid fa-qrcode text-base text-emerald-400 w-5"></i>
                    <span>Attendance Scanner</span>
                </a>
                <a href="<?= base_url('/admin/fees') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/admin/fees', $currentUri) ?>">
                    <i class="fa-solid fa-file-invoice-dollar text-base text-amber-400 w-5"></i>
                    <span>Tuition Fees</span>
                </a>
                <a href="<?= base_url('/teacher/materials') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/teacher/materials', $currentUri) ?>">
                    <i class="fa-solid fa-folder-open text-base text-purple-400 w-5"></i>
                    <span>Study Materials</span>
                </a>
                <a href="<?= base_url('/teacher/quizzes') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/teacher/quizzes', $currentUri) ?>">
                    <i class="fa-solid fa-list-check text-base text-rose-400 w-5"></i>
                    <span>Quizzes & Exams</span>
                </a>
            <?php endif; ?>

            <?php if ($role === 'teacher'): ?>
                <a href="<?= base_url('/teacher/materials') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/teacher/materials', $currentUri) ?>">
                    <i class="fa-solid fa-file-arrow-up text-base text-indigo-400 w-5"></i>
                    <span>Upload Materials</span>
                </a>
                <a href="<?= base_url('/teacher/quizzes') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/teacher/quizzes', $currentUri) ?>">
                    <i class="fa-solid fa-feather-pointed text-base text-rose-400 w-5"></i>
                    <span>Quizzes & MCQ</span>
                </a>
                <a href="<?= base_url('/admin/attendance') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/admin/attendance', $currentUri) ?>">
                    <i class="fa-solid fa-qrcode text-base text-emerald-400 w-5"></i>
                    <span>Class Attendance</span>
                </a>
            <?php endif; ?>

            <?php if ($role === 'student'): ?>
                <a href="<?= base_url('/student/materials') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/student/materials', $currentUri) ?>">
                    <i class="fa-solid fa-book-bookmark text-base text-indigo-400 w-5"></i>
                    <span>My Study Notes</span>
                </a>
                <a href="<?= base_url('/student/take-quiz') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/student/take-quiz', $currentUri) ?>">
                    <i class="fa-solid fa-stopwatch text-base text-amber-400 w-5"></i>
                    <span>Online Quizzes</span>
                </a>
                <a href="<?= base_url('/student/fees') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all <?= is_active('/student/fees', $currentUri) ?>">
                    <i class="fa-solid fa-receipt text-base text-emerald-400 w-5"></i>
                    <span>Fee Receipts</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Quick User info footer in sidebar -->
    <div class="pt-4 border-t border-slate-800/80">
        <div class="rounded-xl bg-slate-950/60 p-3 border border-slate-800 flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-slate-200 truncate"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                <p class="text-[10px] text-slate-400 capitalize">Status: Active</p>
            </div>
        </div>
    </div>
</aside>

<?php
$title = "Admin Dashboard - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-panel p-6 rounded-3xl border border-slate-800 bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">System Dashboard</h1>
            <p class="text-sm text-slate-400 mt-1">Real-time tuition metrics, attendance tracking & fee overview.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('/admin/attendance') ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-qrcode"></i> Scan Attendance
            </a>
            <a href="<?= base_url('/admin/fees') ?>" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-sm shadow-lg shadow-brand-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Collect Fee
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Students -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xl font-bold border border-cyan-500/20">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Students</p>
                <h3 class="text-2xl font-extrabold text-white font-heading mt-0.5"><?= $totalStudents ?></h3>
            </div>
        </div>

        <!-- Total Courses -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl font-bold border border-indigo-500/20">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Classes</p>
                <h3 class="text-2xl font-extrabold text-white font-heading mt-0.5"><?= $totalCourses ?></h3>
            </div>
        </div>

        <!-- Fees Collected -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-500/20">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Fees (<?= date('M Y') ?>)</p>
                <h3 class="text-2xl font-extrabold text-emerald-400 font-heading mt-0.5">$<?= number_format($feeStats['total_collected'], 2) ?></h3>
            </div>
        </div>

        <!-- Today Attendance -->
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-500/20">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Today Present</p>
                <h3 class="text-2xl font-extrabold text-amber-400 font-heading mt-0.5"><?= $attendanceStats['present'] + $attendanceStats['late'] ?> <span class="text-xs text-slate-400 font-normal">scans</span></h3>
            </div>
        </div>
    </div>

    <!-- Data Grids: Recent Payments & Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Payments -->
        <div class="glass-panel rounded-2xl border border-slate-800 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-400"></i> Recent Fee Collections
                </h2>
                <a href="<?= base_url('/admin/fees') ?>" class="text-xs font-bold text-brand-400 hover:text-brand-300">View All →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Receipt</th>
                            <th class="py-3 px-3">Student</th>
                            <th class="py-3 px-3">Amount</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php if (empty($recentPayments)): ?>
                            <tr><td colspan="4" class="py-4 text-center text-slate-500">No payment records found.</td></tr>
                        <?php else: foreach ($recentPayments as $p): ?>
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-3 px-3 font-mono text-xs text-brand-300"><?= htmlspecialchars($p['receipt_number']) ?></td>
                                <td class="py-3 px-3 font-medium text-slate-200"><?= htmlspecialchars($p['student_name']) ?></td>
                                <td class="py-3 px-3 font-bold text-emerald-400">$<?= number_format($p['amount'], 2) ?></td>
                                <td class="py-3 px-3 text-right">
                                    <a href="<?= base_url('/fees/receipt?number=' . urlencode($p['receipt_number'])) ?>" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 hover:bg-indigo-500 hover:text-white transition-colors">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Attendance Logs -->
        <div class="glass-panel rounded-2xl border border-slate-800 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Live Attendance Activity
                </h2>
                <a href="<?= base_url('/admin/attendance') ?>" class="text-xs font-bold text-cyan-400 hover:text-cyan-300">Scanner →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Student</th>
                            <th class="py-3 px-3">Course</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php if (empty($recentAttendance)): ?>
                            <tr><td colspan="4" class="py-4 text-center text-slate-500">No attendance scans recorded today yet.</td></tr>
                        <?php else: foreach ($recentAttendance as $a): ?>
                            <tr class="hover:bg-slate-800/30">
                                <td class="py-3 px-3 font-medium text-slate-200">
                                    <?= htmlspecialchars($a['student_name']) ?>
                                    <span class="block text-[10px] font-mono text-slate-500"><?= htmlspecialchars($a['student_code']) ?></span>
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-300"><?= htmlspecialchars($a['course_title']) ?></td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase <?= $a['status'] === 'present' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($a['status'] === 'late' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20') ?>">
                                        <?= htmlspecialchars($a['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-400"><?= date('h:i A', strtotime($a['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

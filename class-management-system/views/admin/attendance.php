<?php
$title = "Attendance Scanner - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white flex items-center gap-3">
                <i class="fa-solid fa-qrcode text-emerald-400"></i> Smart Attendance Scanner
            </h1>
            <p class="text-sm text-slate-400 mt-1">Scan student ID barcodes or QR cards for instant attendance verification.</p>
        </div>
        <div class="flex items-center gap-3 bg-slate-950 p-2.5 rounded-2xl border border-slate-800">
            <span class="h-3 w-3 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="text-xs font-bold text-emerald-400">Scanner Engine Active</span>
        </div>
    </div>

    <!-- Scanner & Controller Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Scanner Terminal (2 Columns) -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-6 shadow-2xl">
            <!-- Active Class Selector -->
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">Target Tuition Class *</label>
                <select id="scanCourseId" class="w-full rounded-2xl bg-slate-950 border border-slate-700 px-4 py-3.5 text-sm font-semibold text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?> (<?= htmlspecialchars($c['schedule_day']) ?> at <?= date('g:i A', strtotime($c['schedule_time'])) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Barcode Input / Camera Trigger Box -->
            <div class="relative bg-slate-950 p-8 rounded-3xl border-2 border-dashed border-emerald-500/40 text-center space-y-4">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-2xl shadow-lg shadow-emerald-500/10">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white font-heading">Ready for Barcode Scan</h3>
                    <p class="text-xs text-slate-400 mt-1">Use a hardware USB scanner, mobile QR scanner, or type student code (e.g. STU-2026-001) below.</p>
                </div>

                <div class="max-w-md mx-auto relative">
                    <input type="text" id="barcodeInput" placeholder="Scan or enter code (Press Enter)..." autofocus
                           class="w-full rounded-2xl bg-slate-900 border-2 border-emerald-500/50 px-5 py-4 text-base font-mono text-center font-bold text-emerald-300 placeholder-slate-600 focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/20 shadow-xl transition-all">
                </div>

                <div class="flex items-center justify-center gap-4 text-xs font-semibold text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer hover:text-white">
                        <input type="radio" name="scanStatus" value="present" checked class="text-emerald-500"> Mark Present
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer hover:text-white">
                        <input type="radio" name="scanStatus" value="late" class="text-amber-500"> Mark Late
                    </label>
                </div>
            </div>

            <!-- Live Scan Verification Result Card -->
            <div id="scanResultBox" class="hidden p-5 rounded-2xl border transition-all">
                <div class="flex items-center gap-4">
                    <div id="scanResultIcon" class="h-12 w-12 rounded-xl flex items-center justify-center text-xl shrink-0"></div>
                    <div class="overflow-hidden">
                        <h4 id="scanResultName" class="text-lg font-bold text-white font-heading"></h4>
                        <p id="scanResultDetail" class="text-xs text-slate-300 mt-0.5"></p>
                    </div>
                    <span id="scanResultTime" class="ml-auto text-xs font-mono text-slate-400"></span>
                </div>
            </div>
        </div>

        <!-- Attendance Summary & Manual Entry Panel -->
        <div class="space-y-6">
            <!-- Today Stats -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
                <h3 class="font-heading font-bold text-base text-white border-b border-slate-800 pb-3">Today's Scan Summary</h3>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-emerald-950/40 border border-emerald-500/20 p-3.5 rounded-2xl">
                        <span class="text-2xl font-extrabold text-emerald-400 font-heading"><?= $stats['present'] ?></span>
                        <p class="text-[11px] font-bold uppercase text-emerald-500 mt-0.5">Present</p>
                    </div>
                    <div class="bg-amber-950/40 border border-amber-500/20 p-3.5 rounded-2xl">
                        <span class="text-2xl font-extrabold text-amber-400 font-heading"><?= $stats['late'] ?></span>
                        <p class="text-[11px] font-bold uppercase text-amber-500 mt-0.5">Late</p>
                    </div>
                </div>
            </div>

            <!-- Manual Attendance Form -->
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
                <h3 class="font-heading font-bold text-base text-white border-b border-slate-800 pb-3">Manual Attendance Override</h3>
                <form action="<?= base_url('/admin/attendance/mark') ?>" method="POST" class="space-y-3.5">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Student Code</label>
                        <input type="text" name="student_code" placeholder="STU-2026-001" required
                               class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-xs text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Class</label>
                        <select name="course_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-xs text-slate-100">
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-xs text-slate-100">
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors">
                        Record Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Recent Log Table -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h2 class="font-heading font-bold text-lg text-white">Recent Attendance Scan Logs</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/90 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Student Code</th>
                        <th class="py-3 px-4">Class Title</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Scan Time</th>
                        <th class="py-3 px-4">Marked By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" id="attendanceLogTableBody">
                    <?php foreach ($recentLogs as $log): ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-semibold text-white"><?= htmlspecialchars($log['student_name']) ?></td>
                            <td class="py-3 px-4 font-mono text-xs text-indigo-300"><?= htmlspecialchars($log['student_code']) ?></td>
                            <td class="py-3 px-4 text-xs text-slate-300"><?= htmlspecialchars($log['course_title']) ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase <?= $log['status'] === 'present' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($log['status'] === 'late' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20') ?>">
                                    <?= htmlspecialchars($log['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-400"><?= date('M d, Y - h:i A', strtotime($log['created_at'])) ?></td>
                            <td class="py-3 px-4 text-xs text-slate-400"><?= htmlspecialchars($log['marked_by_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    window.SCAN_API_URL = "<?= base_url('/api/attendance/scan') ?>";
</script>
<script src="<?= asset('js/scanner.js') ?>"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

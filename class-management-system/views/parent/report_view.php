<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Report Card - <?= htmlspecialchars($student['name']) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-4 sm:p-6 lg:p-8 selection:bg-indigo-500 selection:text-white">

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Brand Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/20">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="font-heading font-extrabold text-xl text-white">EduClass<span class="text-indigo-400">Pro</span></h1>
                    <p class="text-xs text-slate-400">Parent Student Progress Report</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold uppercase flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span> Live Portal
            </span>
        </div>

        <!-- Student Profile Hero Card -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-900 via-indigo-950/30 to-slate-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-2xl">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-extrabold font-heading shadow-xl">
                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 font-mono text-xs font-bold border border-indigo-500/30">
                        <?= htmlspecialchars($student['student_code']) ?>
                    </span>
                    <h2 class="text-2xl font-bold font-heading text-white mt-1"><?= htmlspecialchars($student['name']) ?></h2>
                    <p class="text-xs text-slate-400 mt-0.5"><i class="fa-solid fa-layer-group text-slate-500 mr-1"></i>Grade: <?= htmlspecialchars($student['grade_level']) ?></p>
                </div>
            </div>

            <div class="w-full sm:w-auto flex flex-col gap-2">
                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Parent Report Link copied to clipboard!')"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-link text-indigo-400"></i> Copy Permanent Parent Link
                </button>
            </div>
        </div>

        <!-- Section 1: Attendance Summary -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-qrcode text-emerald-400"></i> Class Attendance Report
                </h3>
                <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                    <?= $attendanceRate ?>% Attendance Rate
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800">
                    <span class="text-2xl font-extrabold text-white font-heading"><?= $totalSessions ?></span>
                    <p class="text-[11px] font-bold uppercase text-slate-400 mt-1">Total Sessions</p>
                </div>
                <div class="bg-emerald-950/40 p-4 rounded-2xl border border-emerald-500/20">
                    <span class="text-2xl font-extrabold text-emerald-400 font-heading"><?= $presentCount ?></span>
                    <p class="text-[11px] font-bold uppercase text-emerald-500 mt-1">Present</p>
                </div>
                <div class="bg-amber-950/40 p-4 rounded-2xl border border-amber-500/20">
                    <span class="text-2xl font-extrabold text-amber-400 font-heading"><?= $lateCount ?></span>
                    <p class="text-[11px] font-bold uppercase text-amber-500 mt-1">Late Arrival</p>
                </div>
                <div class="bg-rose-950/40 p-4 rounded-2xl border border-rose-500/20">
                    <span class="text-2xl font-extrabold text-rose-400 font-heading"><?= $absentCount ?></span>
                    <p class="text-[11px] font-bold uppercase text-rose-500 mt-1">Absent</p>
                </div>
            </div>

            <!-- Attendance Logs List -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase text-slate-400">Recent Attendance Activity</h4>
                <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                    <?php if (empty($attendanceLogs)): ?>
                        <p class="text-xs text-slate-500 italic p-3 text-center">No attendance scans recorded yet.</p>
                    <?php else: foreach ($attendanceLogs as $att): ?>
                        <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-200"><?= htmlspecialchars($att['course_title']) ?></span>
                                <span class="block text-[10px] text-slate-500 font-mono"><?= date('M d, Y - h:i A', strtotime($att['created_at'])) ?></span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full font-bold uppercase text-[10px] <?= $att['status'] === 'present' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($att['status'] === 'late' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20') ?>">
                                <?= htmlspecialchars($att['status']) ?>
                            </span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Section 2: Tuition Fee Payments -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-indigo-400"></i> Tuition Fee Payments
                </h3>
                <span class="text-xs font-mono font-bold text-indigo-300">
                    Total Paid: <?= format_currency($totalPaidAmount) ?>
                </span>
            </div>

            <!-- Current Month Status Badges -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase text-slate-400">Enrolled Courses Monthly Status (<?= date('F Y') ?>)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($enrolledCourses as $c): ?>
                        <?php
                            $isPaid = false;
                            foreach ($payments as $p) {
                                if ($p['course_id'] == $c['id'] && $p['month'] == date('Y-m') && $p['status'] === 'paid') {
                                    $isPaid = true; break;
                                }
                            }
                        ?>
                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-slate-200"><?= htmlspecialchars($c['title']) ?></p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5"><?= format_currency($c['monthly_fee']) ?> / month</p>
                            </div>
                            <?php if ($isPaid): ?>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold uppercase text-[10px]">PAID ✓</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold uppercase text-[10px]">DUE</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Section 3: Online Quizzes & Performance -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4 shadow-xl">
            <h3 class="font-heading font-bold text-lg text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-trophy text-amber-400"></i> Online Quiz & Exam Performance
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Quiz Title</th>
                            <th class="py-3 px-3">Course</th>
                            <th class="py-3 px-3">Score %</th>
                            <th class="py-3 px-3">Result</th>
                            <th class="py-3 px-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php if (empty($quizAttempts)): ?>
                            <tr><td colspan="5" class="py-4 text-center text-slate-500 text-xs">No quiz attempts submitted yet.</td></tr>
                        <?php else: foreach ($quizAttempts as $qa): ?>
                            <tr class="hover:bg-slate-800/30 text-xs">
                                <td class="py-3 px-3 font-bold text-white"><?= htmlspecialchars($qa['quiz_title']) ?></td>
                                <td class="py-3 px-3 text-slate-400"><?= htmlspecialchars($qa['course_title']) ?></td>
                                <td class="py-3 px-3 font-bold font-mono text-indigo-300"><?= number_format($qa['score'], 1) ?>%</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $qa['score'] >= $qa['pass_mark'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' ?>">
                                        <?= $qa['score'] >= $qa['pass_mark'] ? 'PASSED' : 'FAILED' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-500"><?= date('M d, Y', strtotime($qa['submitted_at'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

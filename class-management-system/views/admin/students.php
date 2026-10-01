<?php
$title = "Student Management - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-panel p-6 rounded-3xl border border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Student Directory</h1>
            <p class="text-sm text-slate-400 mt-1">Manage student registrations, barcodes, and tuition class enrollments.</p>
        </div>
        <button onclick="openModal('registerStudentModal')" class="px-5 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition-all flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Register New Student
        </button>
    </div>

    <!-- Student List Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-heading font-bold text-lg text-white">All Enrolled Students (<?= count($students) ?>)</h2>
            <div class="relative w-64">
                <input type="text" id="studentSearch" onkeyup="filterStudents()" placeholder="Search name or code..."
                       class="w-full rounded-xl bg-slate-950 border border-slate-700/80 px-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300" id="studentTable">
                <thead class="bg-slate-900/90 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Student ID & Code</th>
                        <th class="py-3.5 px-4">Student Name & Contact</th>
                        <th class="py-3.5 px-4">Grade Level</th>
                        <th class="py-3.5 px-4">Enrolled Classes</th>
                        <th class="py-3.5 px-4 text-center">Barcode</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($students)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">No students registered yet.</td></tr>
                    <?php else: foreach ($students as $st): ?>
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 font-mono">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-brand-300 text-xs font-bold">
                                    <?= htmlspecialchars($st['student_code']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-100"><?= htmlspecialchars($st['name']) ?></p>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($st['email']) ?></p>
                                <?php if ($st['phone']): ?>
                                    <p class="text-[11px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[9px] mr-1"></i><?= htmlspecialchars($st['phone']) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-semibold">
                                    <?= htmlspecialchars($st['grade_level']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php if (empty($st['enrolled_courses'])): ?>
                                        <span class="text-xs text-slate-500 italic">No courses</span>
                                    <?php else: foreach ($st['enrolled_courses'] as $c): ?>
                                        <span class="px-2 py-0.5 rounded bg-indigo-950 text-indigo-300 border border-indigo-800 text-[11px]">
                                            <?= htmlspecialchars($c['course_code']) ?>
                                        </span>
                                    <?php endforeach; endif; ?>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <button onclick="showBarcodeModal('<?= htmlspecialchars($st['student_code']) ?>', '<?= htmlspecialchars($st['name']) ?>')"
                                        class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white transition-colors text-xs font-semibold flex items-center gap-1.5 mx-auto border border-emerald-500/20">
                                    <i class="fa-solid fa-barcode"></i> Code
                                </button>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button onclick="openEnrollModal(<?= $st['id'] ?>, '<?= htmlspecialchars($st['name']) ?>')"
                                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-brand-600 text-slate-200 hover:text-white text-xs font-semibold transition-colors border border-slate-700">
                                    + Course
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal 1: Register Student Modal -->
<div id="registerStudentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-lg glass-panel rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-xl font-bold font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-brand-400"></i> Register New Student
            </h3>
            <button onclick="closeModal('registerStudentModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('/admin/students/store') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="John Smith"
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="john@student.com"
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password</label>
                    <input type="password" name="password" value="password123" required
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Grade Level *</label>
                    <select name="grade_level" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12 / A/L</option>
                        <option value="Revision Class">Revision Class</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Student Phone</label>
                    <input type="text" name="phone" placeholder="+1 555-0192"
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Parent Phone</label>
                    <input type="text" name="parent_phone" placeholder="+1 555-0193"
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Tuition Classes to Enroll</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-36 overflow-y-auto p-2 bg-slate-950 rounded-xl border border-slate-800">
                    <?php foreach ($courses as $c): ?>
                        <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer p-1">
                            <input type="checkbox" name="course_ids[]" value="<?= $c['id'] ?>" class="rounded bg-slate-900 border-slate-700 text-brand-500">
                            <span><?= htmlspecialchars($c['title']) ?> ($<?= $c['monthly_fee'] ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('registerStudentModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-sm font-semibold hover:bg-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold shadow-lg shadow-brand-500/20">
                    Create Student & Barcode
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Barcode View Modal -->
<div id="barcodeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-sm glass-panel rounded-3xl border border-slate-800 p-6 space-y-6 text-center shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold font-heading text-white">Student Barcode ID</h3>
            <button onclick="closeModal('barcodeModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="bg-white p-6 rounded-2xl space-y-3 shadow-inner">
            <p id="barcodeStudentName" class="font-extrabold text-slate-900 text-lg"></p>
            
            <!-- Dynamic SVG Barcode Rendering -->
            <div id="barcodeSvgContainer" class="flex justify-center my-2"></div>

            <p id="barcodeStudentCode" class="font-mono font-bold text-slate-800 text-base tracking-widest"></p>
        </div>

        <button onclick="window.print()" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-print"></i> Print ID Card Barcode
        </button>
    </div>
</div>

<!-- Modal 3: Quick Course Enroll Modal -->
<div id="enrollModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-md glass-panel rounded-3xl border border-slate-800 p-6 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold font-heading text-white">Enroll Course for <span id="enrollStudentName" class="text-brand-400"></span></h3>
            <button onclick="closeModal('enrollModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('/admin/students/enroll') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="student_id" id="enrollStudentId">

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Tuition Class</label>
                <select name="course_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?> - $<?= $c['monthly_fee'] ?>/mo (<?= htmlspecialchars($c['schedule_day']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('enrollModal')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 text-white font-bold text-sm">Confirm Enrollment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterStudents() {
        const query = document.getElementById('studentSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#studentTable tbody tr');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function showBarcodeModal(code, name) {
        document.getElementById('barcodeStudentName').innerText = name;
        document.getElementById('barcodeStudentCode').innerText = code;

        // Render Code 128 style Barcode SVG lines dynamically
        const svgContainer = document.getElementById('barcodeSvgContainer');
        let bars = '';
        for (let i = 0; i < 35; i++) {
            const width = (i % 3 === 0) ? 3 : ((i % 2 === 0) ? 1.5 : 2);
            bars += `<rect x="${i * 7}" y="0" width="${width}" height="60" fill="#000" />`;
        }
        svgContainer.innerHTML = `<svg width="250" height="60" viewBox="0 0 250 60">${bars}</svg>`;

        openModal('barcodeModal');
    }

    function openEnrollModal(id, name) {
        document.getElementById('enrollStudentId').value = id;
        document.getElementById('enrollStudentName').innerText = name;
        openModal('enrollModal');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

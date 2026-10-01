<?php
$title = "Tuition Fee Management - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-panel p-6 rounded-3xl border border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Tuition Fee Ledger</h1>
            <p class="text-sm text-slate-400 mt-1">Process student monthly fee payments, issue official receipts & monitor pending dues.</p>
        </div>
        <button onclick="openModal('collectFeeModal')" class="px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Collect Fee Payment
        </button>
    </div>

    <!-- Monthly Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-panel p-5 rounded-2xl border border-slate-800">
            <p class="text-xs font-bold uppercase text-slate-400">Total Revenue (<?= htmlspecialchars($stats['month']) ?>)</p>
            <h3 class="text-3xl font-extrabold text-emerald-400 font-heading mt-1">$<?= number_format($stats['total_collected'], 2) ?></h3>
            <p class="text-xs text-slate-500 mt-1"><?= $stats['total_payments'] ?> payment transaction(s)</p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border border-slate-800">
            <p class="text-xs font-bold uppercase text-slate-400">Expected Class Revenue</p>
            <h3 class="text-3xl font-extrabold text-indigo-400 font-heading mt-1">$<?= number_format($stats['expected_total'], 2) ?></h3>
            <p class="text-xs text-slate-500 mt-1">Based on active course enrollments</p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border border-slate-800">
            <p class="text-xs font-bold uppercase text-slate-400">Estimated Pending Dues</p>
            <h3 class="text-3xl font-extrabold text-amber-400 font-heading mt-1">$<?= number_format($stats['pending_dues'], 2) ?></h3>
            <p class="text-xs text-slate-500 mt-1">Uncollected monthly fees</p>
        </div>
    </div>

    <!-- Payment Ledger Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <h2 class="font-heading font-bold text-lg text-white">Payment Transaction History</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/90 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Receipt No.</th>
                        <th class="py-3.5 px-4">Student Name</th>
                        <th class="py-3.5 px-4">Course / Class</th>
                        <th class="py-3.5 px-4">Month</th>
                        <th class="py-3.5 px-4">Amount Paid</th>
                        <th class="py-3.5 px-4">Method</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="8" class="py-8 text-center text-slate-500">No payment records found.</td></tr>
                    <?php else: foreach ($payments as $p): ?>
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-4 px-4 font-mono font-bold text-brand-300 text-xs">
                                <?= htmlspecialchars($p['receipt_number']) ?>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-white"><?= htmlspecialchars($p['student_name']) ?></p>
                                <p class="text-xs font-mono text-slate-500"><?= htmlspecialchars($p['student_code']) ?></p>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-300"><?= htmlspecialchars($p['course_title']) ?></td>
                            <td class="py-4 px-4 font-mono text-xs text-slate-300"><?= htmlspecialchars($p['month']) ?></td>
                            <td class="py-4 px-4 font-bold text-emerald-400">$<?= number_format($p['amount'], 2) ?></td>
                            <td class="py-4 px-4 text-xs uppercase font-bold text-slate-400"><?= htmlspecialchars($p['payment_method']) ?></td>
                            <td class="py-4 px-4 text-xs font-mono text-slate-400"><?= htmlspecialchars($p['payment_date']) ?></td>
                            <td class="py-4 px-4 text-right">
                                <a href="<?= base_url('/fees/receipt?number=' . urlencode($p['receipt_number'])) ?>" target="_blank"
                                   class="px-3 py-1.5 rounded-xl bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600 hover:text-white text-xs font-bold transition-colors border border-indigo-500/30 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-print"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal: Collect Fee Modal -->
<div id="collectFeeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-lg glass-panel rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-xl font-bold font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-cash-register text-emerald-400"></i> Record Fee Payment
            </h3>
            <button onclick="closeModal('collectFeeModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('/admin/fees/pay') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Student *</label>
                <select name="student_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?> (<?= htmlspecialchars($st['student_code']) ?> - <?= htmlspecialchars($st['grade_level']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Course / Class *</label>
                <select name="course_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?> ($<?= $c['monthly_fee'] ?>/mo)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Payment Month *</label>
                    <input type="month" name="month" value="<?= date('Y-m') ?>" required
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Amount ($) *</label>
                    <input type="number" step="0.01" name="amount" placeholder="45.00" required
                           class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Payment Method</label>
                <select name="payment_method" class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100">
                    <option value="cash">Cash Counter</option>
                    <option value="card">Credit / Debit Card</option>
                    <option value="online">Online Payment</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Payment Notes (Optional)</label>
                <input type="text" name="notes" placeholder="e.g. October fee paid at counter"
                       class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('collectFeeModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/20">
                    Issue Receipt & Record
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

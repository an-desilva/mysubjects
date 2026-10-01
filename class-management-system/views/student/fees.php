<?php
$title = "My Fee Status & Receipts - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800">
        <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">My Tuition Fees & Receipts</h1>
        <p class="text-sm text-slate-400 mt-1">Review your monthly tuition payment history and access official digital receipts.</p>
    </div>

    <!-- Monthly Status for Enrolled Courses -->
    <div class="glass-panel rounded-3xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <h2 class="font-heading font-bold text-lg text-white">Current Month Status (<?= date('F Y') ?>)</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($enrolledCourses as $c): ?>
                <?php
                    // Check if student paid for this course for current month
                    $isPaid = false;
                    $paidReceipt = null;
                    foreach ($payments as $p) {
                        if ($p['course_id'] == $c['id'] && $p['month'] == date('Y-m') && $p['status'] === 'paid') {
                            $isPaid = true;
                            $paidReceipt = $p['receipt_number'];
                            break;
                        }
                    }
                ?>
                <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white text-sm"><?= htmlspecialchars($c['title']) ?></h3>
                        <p class="text-xs font-mono text-slate-400 mt-0.5">$<?= number_format($c['monthly_fee'], 2) ?> / month</p>
                    </div>
                    <?php if ($isPaid): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold uppercase">
                            PAID ✓
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-bold uppercase">
                            DUE
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Full Payment History Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 p-6 space-y-4 shadow-xl">
        <h2 class="font-heading font-bold text-lg text-white">Payment Transaction History</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/90 text-xs uppercase text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Receipt Number</th>
                        <th class="py-3.5 px-4">Tuition Class</th>
                        <th class="py-3.5 px-4">Fee Month</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-right">View Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">No payment history recorded.</td></tr>
                    <?php else: foreach ($payments as $p): ?>
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-4 px-4 font-mono font-bold text-brand-300 text-xs"><?= htmlspecialchars($p['receipt_number']) ?></td>
                            <td class="py-4 px-4 font-semibold text-white"><?= htmlspecialchars($p['course_title']) ?></td>
                            <td class="py-4 px-4 font-mono text-xs text-slate-300"><?= htmlspecialchars($p['month']) ?></td>
                            <td class="py-4 px-4 font-bold text-emerald-400">$<?= number_format($p['amount'], 2) ?></td>
                            <td class="py-4 px-4 text-xs font-mono text-slate-400"><?= htmlspecialchars($p['payment_date']) ?></td>
                            <td class="py-4 px-4 text-right">
                                <a href="<?= base_url('/fees/receipt?number=' . urlencode($p['receipt_number'])) ?>" target="_blank"
                                   class="px-3 py-1.5 rounded-xl bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600 hover:text-white text-xs font-bold transition-colors border border-indigo-500/30 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

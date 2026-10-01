<!DOCTYPE html>
<html lang="en" class="bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?= htmlspecialchars($receipt['receipt_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .receipt-card { border: 1px solid #ccc !important; box-shadow: none !important; color: black !important; background: white !important; }
            .text-emerald-400, .text-brand-400 { color: black !important; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-8 space-y-6 shadow-2xl receipt-card">
        <!-- Top Receipt Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-white">EduClass<span class="text-indigo-500">Pro</span></h2>
                <p class="text-xs text-slate-400">Official Tuition Payment Receipt</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold uppercase">PAID</span>
                <p class="text-xs font-mono text-slate-400 mt-1"><?= htmlspecialchars($receipt['receipt_number']) ?></p>
            </div>
        </div>

        <!-- Receipt Details Grid -->
        <div class="space-y-4 text-sm">
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Student Name:</span>
                <span class="font-bold text-white"><?= htmlspecialchars($receipt['student_name']) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Student Code:</span>
                <span class="font-mono text-brand-300"><?= htmlspecialchars($receipt['student_code']) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Class / Subject:</span>
                <span class="font-semibold text-slate-200"><?= htmlspecialchars($receipt['course_title']) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Fee Period:</span>
                <span class="font-mono text-slate-300"><?= htmlspecialchars($receipt['month']) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Payment Method:</span>
                <span class="uppercase font-bold text-slate-300"><?= htmlspecialchars($receipt['payment_method']) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-800/60">
                <span class="text-slate-400">Payment Date:</span>
                <span class="font-mono text-slate-400"><?= htmlspecialchars($receipt['payment_date']) ?></span>
            </div>

            <div class="flex justify-between items-center py-4 bg-slate-950 p-4 rounded-2xl border border-slate-800 mt-4">
                <span class="text-base font-bold text-slate-300">Total Amount Paid:</span>
                <span class="text-2xl font-extrabold text-emerald-400 font-mono"><?= format_currency($receipt['amount']) ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-800 no-print">
            <a href="<?= base_url('/admin/fees') ?>" class="text-xs font-semibold text-slate-400 hover:text-white">
                ← Back to Fees Ledger
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</body>
</html>

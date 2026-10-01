<?php
$title = "My Study Materials - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800">
        <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">My Study Resources & Past Papers</h1>
        <p class="text-sm text-slate-400 mt-1">Access permitted lecture notes, revision PDFs, and past question papers for your enrolled tuition classes.</p>
    </div>

    <!-- Materials Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($materials)): ?>
            <div class="col-span-full glass-panel p-12 text-center rounded-3xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-folder-open text-4xl text-slate-600"></i>
                <h3 class="text-lg font-bold text-slate-300">No Materials Found</h3>
                <p class="text-xs text-slate-500">Your teachers haven't uploaded resources for your enrolled classes yet.</p>
            </div>
        <?php else: foreach ($materials as $m): ?>
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4 hover:border-indigo-500/40 transition-all shadow-lg">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $m['type'] === 'past_paper' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' ?>">
                            <?= str_replace('_', ' ', $m['type']) ?>
                        </span>
                        <span class="text-[11px] font-mono text-slate-500"><?= round($m['file_size'] / 1024 / 1024, 2) ?> MB</span>
                    </div>

                    <div>
                        <h3 class="font-heading font-bold text-lg text-white leading-snug"><?= htmlspecialchars($m['title']) ?></h3>
                        <p class="text-xs text-indigo-300 font-semibold mt-1">
                            <i class="fa-solid fa-chalkboard-user text-[10px] mr-1"></i><?= htmlspecialchars($m['course_title']) ?> (By <?= htmlspecialchars($m['teacher_name']) ?>)
                        </p>
                        <?php if (!empty($m['description'])): ?>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2"><?= htmlspecialchars($m['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <span><i class="fa-regular fa-clock mr-1"></i><?= date('M d, Y', strtotime($m['created_at'])) ?></span>
                    <a href="<?= base_url('/materials/download?id=' . $m['id']) ?>" target="_blank"
                       class="px-4 py-2 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold transition-all shadow-md shadow-brand-500/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

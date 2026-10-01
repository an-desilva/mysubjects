<?php
$title = "Study Materials & Past Papers - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 glass-panel p-6 rounded-3xl border border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Study Materials & Past Papers</h1>
            <p class="text-sm text-slate-400 mt-1">Upload PDF lecture notes, past question papers, and revision guides for students.</p>
        </div>
        <button onclick="openModal('uploadMaterialModal')" class="px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
            <i class="fa-solid fa-file-arrow-up"></i> Upload PDF Resource
        </button>
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($materials)): ?>
            <div class="col-span-full glass-panel p-12 text-center rounded-3xl border border-slate-800 space-y-3">
                <i class="fa-solid fa-folder-open text-4xl text-slate-600"></i>
                <h3 class="text-lg font-bold text-slate-300">No Materials Uploaded Yet</h3>
                <p class="text-xs text-slate-500">Click the button above to upload lecture notes or past papers.</p>
            </div>
        <?php else: foreach ($materials as $m): ?>
            <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4 hover:border-indigo-500/40 transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?= $m['type'] === 'past_paper' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' ?>">
                            <?= str_replace('_', ' ', $m['type']) ?>
                        </span>
                        <span class="text-[11px] font-mono text-slate-500"><?= round($m['file_size'] / 1024 / 1024, 2) ?> MB</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white leading-snug"><?= htmlspecialchars($m['title']) ?></h3>
                        <p class="text-xs text-indigo-300 font-semibold mt-1"><i class="fa-solid fa-book text-[10px] mr-1"></i><?= htmlspecialchars($m['course_title']) ?></p>
                        <?php if (!empty($m['description'])): ?>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2"><?= htmlspecialchars($m['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <span><i class="fa-regular fa-clock mr-1"></i><?= date('M d, Y', strtotime($m['created_at'])) ?></span>
                    <a href="<?= base_url('/materials/download?id=' . $m['id']) ?>" target="_blank"
                       class="px-3.5 py-1.5 rounded-xl bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600 hover:text-white font-bold transition-all border border-indigo-500/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>

<!-- Modal: Upload Material -->
<div id="uploadMaterialModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-lg glass-panel rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-xl font-bold font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-file-arrow-up text-indigo-400"></i> Upload Study Material
            </h3>
            <button onclick="closeModal('uploadMaterialModal')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('/teacher/materials/upload') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Resource Title *</label>
                <input type="text" name="title" required placeholder="e.g. Physics Mechanics Summary Notes"
                       class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Target Class / Subject *</label>
                <select name="course_id" required class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none">
                    <option value="">-- Choose Class --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Resource Category</label>
                    <select name="type" class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100">
                        <option value="pdf">PDF Notes</option>
                        <option value="past_paper">Past Paper</option>
                        <option value="notes">Summary Sheet</option>
                        <option value="assignment">Homework Assignment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select File (.pdf) *</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx" required
                           class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" placeholder="Brief note about the PDF contents..."
                          class="w-full rounded-xl bg-slate-950 border border-slate-700 px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal('uploadMaterialModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 text-sm">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-500/20">
                    Upload Resource
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

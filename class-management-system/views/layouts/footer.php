    </div><!-- End Main Wrapper -->

    <!-- Global Toast Alerts -->
    <?php $successMsg = get_flash('success'); $errorMsg = get_flash('error'); ?>
    <?php if ($successMsg || $errorMsg): ?>
        <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm">
            <?php if ($successMsg): ?>
                <div class="flex items-center gap-3 rounded-xl bg-emerald-950/90 text-emerald-200 border border-emerald-500/30 p-4 shadow-2xl backdrop-blur-md animate-bounce-short">
                    <i class="fa-solid fa-circle-check text-xl text-emerald-400 shrink-0"></i>
                    <p class="text-sm font-medium"><?= htmlspecialchars($successMsg) ?></p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-200">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="flex items-center gap-3 rounded-xl bg-rose-950/90 text-rose-200 border border-rose-500/30 p-4 shadow-2xl backdrop-blur-md animate-bounce-short">
                    <i class="fa-solid fa-circle-exclamation text-xl text-rose-400 shrink-0"></i>
                    <p class="text-sm font-medium"><?= htmlspecialchars($errorMsg) ?></p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-rose-400 hover:text-rose-200">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-container');
                if (toast) {
                    toast.style.transition = 'opacity 0.5s ease-out';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <!-- Modal Helper Script -->
    <script>
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            }
        }
        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }
    </script>
</body>
</html>

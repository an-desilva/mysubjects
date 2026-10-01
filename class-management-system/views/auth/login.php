<?php
$title = "Sign In - " . APP_NAME;
require __DIR__ . '/../layouts/header.php';
?>

<main class="w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 min-h-[calc(100vh-65px)] bg-slate-950 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(99,102,241,0.25),rgba(255,255,255,0))]">
    <div class="w-full max-w-md space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-brand-600 to-purple-600 text-white shadow-xl shadow-brand-500/30">
                <i class="fa-solid fa-graduation-cap text-2xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold font-heading text-white tracking-tight">Welcome Back</h1>
            <p class="text-sm text-slate-400">Class & Tuition Management Portal</p>
        </div>

        <!-- Quick Credentials Presets for Demo -->
        <div class="glass-panel p-4 rounded-2xl border border-slate-800 space-y-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center">⚡ Quick Test Login Presets</p>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <button type="button" onclick="fillLogin('admin@tuition.com', 'password123')" class="px-2 py-2 rounded-xl bg-slate-800 hover:bg-brand-600/30 hover:border-brand-500 border border-slate-700 text-slate-200 transition-all font-semibold flex flex-col items-center gap-1">
                    <i class="fa-solid fa-user-shield text-indigo-400"></i>
                    <span>Admin</span>
                </button>
                <button type="button" onclick="fillLogin('john.doe@tuition.com', 'password123')" class="px-2 py-2 rounded-xl bg-slate-800 hover:bg-brand-600/30 hover:border-brand-500 border border-slate-700 text-slate-200 transition-all font-semibold flex flex-col items-center gap-1">
                    <i class="fa-solid fa-chalkboard-user text-emerald-400"></i>
                    <span>Teacher</span>
                </button>
                <button type="button" onclick="fillLogin('alex.smith@student.com', 'password123')" class="px-2 py-2 rounded-xl bg-slate-800 hover:bg-brand-600/30 hover:border-brand-500 border border-slate-700 text-slate-200 transition-all font-semibold flex flex-col items-center gap-1">
                    <i class="fa-solid fa-user-graduate text-cyan-400"></i>
                    <span>Student</span>
                </button>
            </div>
        </div>

        <!-- Login Form Card -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-2xl space-y-6">
            <form action="<?= base_url('/login') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="user@tuition.com"
                               class="w-full rounded-xl bg-slate-900/80 border border-slate-700/80 pl-10 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                               class="w-full rounded-xl bg-slate-900/80 border border-slate-700/80 pl-10 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-brand-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                    Sign In to Portal <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    function fillLogin(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

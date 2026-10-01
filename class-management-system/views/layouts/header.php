<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex flex-col antialiased selection:bg-brand-500 selection:text-white">
    <!-- Top Global Header / Mobile Navbar -->
    <header class="glass-panel sticky top-0 z-40 w-full border-b border-slate-800 bg-slate-900/80 px-4 py-3 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-brand-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/25 group-hover:scale-105 transition-transform">
                        <i class="fa-solid font-bold fa-graduation-cap text-lg"></i>
                    </div>
                    <div>
                        <span class="font-heading font-extrabold text-xl tracking-tight bg-gradient-to-r from-white via-slate-200 to-indigo-200 bg-clip-text text-transparent">
                            EduClass<span class="text-brand-500">Pro</span>
                        </span>
                        <span class="hidden sm:inline-block ml-2 text-xs px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 font-semibold border border-brand-500/20">Tuition v2.5</span>
                    </div>
                </a>
            </div>

            <?php if (is_logged_in()): 
                $user = auth_user(); 
                $userName = $user['name'] ?? $user['email'] ?? 'User';
                $userRole = $user['role'] ?? 'guest';
            ?>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex flex-col text-right">
                    <span class="text-sm font-semibold text-slate-200"><?= htmlspecialchars($userName) ?></span>
                    <span class="text-xs uppercase tracking-wider font-bold text-indigo-400"><?= htmlspecialchars($userRole) ?></span>
                </div>
                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white shadow-md">
                    <?= strtoupper(substr($userName, 0, 1)) ?>
                </div>
                <a href="<?= base_url('/logout') ?>" title="Logout" class="h-9 w-9 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors border border-rose-500/20">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Wrapper -->
    <div class="flex flex-1 min-h-[calc(100vh-65px)]">

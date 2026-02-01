<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Vending Machine') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f' }
                    }
                }
            }
        }
    </script>
    <style>
        .nav-glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.06); }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.05); }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 0.75rem; padding: 0.625rem 1.25rem; background: #f59e0b; color: white; box-shadow: 0 10px 15px -3px rgba(245,158,11,0.25); transition: all 0.2s; }
        .btn-primary:hover { background: #d97706; box-shadow: 0 10px 15px -3px rgba(245,158,11,0.3); }
        .btn-secondary { display: inline-flex; align-items: center; justify-content: center; font-weight: 500; border-radius: 0.75rem; padding: 0.625rem 1.25rem; background: #f1f5f9; color: #334155; transition: all 0.2s; }
        .btn-secondary:hover { background: #e2e8f0; }
        .input-field { width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: rgba(255,255,255,0.8); outline: none; transition: all 0.2s; }
        .input-field:focus { border-color: #fbbf24; box-shadow: 0 0 0 4px rgba(251,191,36,0.2); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-amber-50/30 to-slate-50 min-h-screen font-sans text-slate-800 antialiased">
<div class="min-h-screen flex flex-col">
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="/products" class="flex items-center gap-2 text-slate-800 hover:text-amber-600 transition-colors">
                    <span class="text-2xl">🥤</span>
                    <span class="font-bold text-xl tracking-tight">Vending</span>
                </a>
                <div class="flex items-center gap-6">
                    <?php if (!empty($user)): ?>
                        <a href="/products" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Products</a>
                        <a href="/transactions" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Transactions</a>
                        <?php if ($user->isAdmin()): ?>
                            <a href="/admin" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Dashboard</a>
                            <a href="/products/create" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Add Product</a>
                            <a href="/users" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Users</a>
                        <?php endif; ?>
                        <span class="text-slate-500 text-sm"><?= htmlspecialchars($user->username) ?></span>
                        <a href="/logout" class="btn-secondary text-sm">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="text-slate-600 hover:text-amber-600 font-medium transition-colors">Login</a>
                        <a href="/register" class="btn-primary text-sm">Sign up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8">

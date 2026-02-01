<?php $pageTitle = 'Login'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vending Machine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', system-ui, sans-serif; }
        .input-field { width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: white; outline: none; transition: all 0.2s; }
        .input-field:focus { border-color: #fbbf24; box-shadow: 0 0 0 4px rgba(251,191,36,0.2); }
        .btn-primary { width: 100%; padding: 0.875rem; border-radius: 0.75rem; font-weight: 600; background: #f59e0b; color: white; box-shadow: 0 10px 15px -3px rgba(245,158,11,0.25); transition: all 0.2s; }
        .btn-primary:hover { background: #d97706; box-shadow: 0 10px 15px -3px rgba(245,158,11,0.3); }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-amber-50/40 to-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 text-slate-800">
                <span class="text-4xl">🥤</span>
                <span class="font-bold text-2xl tracking-tight">Vending</span>
            </a>
        </div>
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Welcome back</h1>
            <p class="text-slate-500 text-sm mb-6">Sign in to continue to the store.</p>
            <?php if (!empty($error)): ?>
                <div class="mb-5 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="/login" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username" placeholder="Enter your username" class="input-field">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="input-field">
                </div>
                <button type="submit" class="btn-primary">Sign in</button>
            </form>
            <p class="mt-6 text-center text-slate-500 text-sm">
                Don't have an account? <a href="/register" class="font-semibold text-amber-600 hover:text-amber-700">Create one</a>
            </p>
        </div>
    </div>
</body>
</html>

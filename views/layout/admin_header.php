<?php
$pageTitle = $pageTitle ?? 'Admin';
if (!isset($user)) $user = null;
if (!isset($currentPage)) $currentPage = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex-shrink-0 min-h-screen flex flex-col">
        <div class="p-4 border-b border-slate-700">
            <a href="/admin" class="text-xl font-bold text-amber-400">Vending Admin</a>
        </div>
        <nav class="p-3 flex-1">
            <ul class="space-y-1">
                <li>
                    <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'dashboard' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/products" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'products' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>📦</span> Products
                    </a>
                </li>
                <li>
                    <a href="/products/create" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'product-create' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>➕</span> Register Product
                    </a>
                </li>
                <li>
                    <a href="/admin/inventory" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'inventory' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>📋</span> Inventory
                    </a>
                </li>
                <li>
                    <a href="/admin/transactions" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'transactions' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>💰</span> Transactions
                    </a>
                </li>
                <li>
                    <a href="/users" class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentPage === 'users' ? 'bg-slate-700 text-amber-400' : 'hover:bg-slate-700 text-slate-200' ?>">
                        <span>👥</span> Users
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-3 border-t border-slate-700">
            <a href="/products" class="block px-3 py-2 text-slate-400 hover:text-white text-sm">← Back to Store</a>
            <a href="/logout" class="block px-3 py-2 text-slate-400 hover:text-white text-sm">Logout</a>
        </div>
    </aside>
    <!-- Main content -->
    <main class="flex-1 overflow-auto p-6">

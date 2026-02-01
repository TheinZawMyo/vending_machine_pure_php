<div>
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-amber-500">
            <p class="text-slate-500 text-sm font-medium">Total Products</p>
            <p class="text-2xl font-bold text-slate-800"><?= (int) $totalProducts ?></p>
            <a href="/products" class="text-amber-600 text-sm hover:underline mt-1 inline-block">View →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <p class="text-slate-500 text-sm font-medium">Low Stock (&lt;10)</p>
            <p class="text-2xl font-bold text-slate-800"><?= (int) $lowStockCount ?></p>
            <a href="/admin/inventory" class="text-amber-600 text-sm hover:underline mt-1 inline-block">View →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
            <p class="text-slate-500 text-sm font-medium">Total Transactions</p>
            <p class="text-2xl font-bold text-slate-800"><?= (int) $totalTransactions ?></p>
            <a href="/admin/transactions" class="text-amber-600 text-sm hover:underline mt-1 inline-block">View →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-slate-500 text-sm font-medium">Total Users</p>
            <p class="text-2xl font-bold text-slate-800"><?= (int) $totalUsers ?></p>
            <a href="/users" class="text-amber-600 text-sm hover:underline mt-1 inline-block">View →</a>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Recent Transactions</h2>
        <?php if (empty($recentTransactions)): ?>
            <p class="text-slate-500">No transactions yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Product ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">User ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Qty</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Total (USD)</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $tx): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4 text-slate-700"><?= (int) $tx->id ?></td>
                            <td class="py-3 px-4 text-slate-700"><?= (int) $tx->productId ?></td>
                            <td class="py-3 px-4 text-slate-700"><?= $tx->userId !== null ? (int) $tx->userId : '—' ?></td>
                            <td class="py-3 px-4 text-slate-700"><?= (int) $tx->quantity ?></td>
                            <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($tx->totalAmount) ?></td>
                            <td class="py-3 px-4 text-slate-600 text-sm"><?= htmlspecialchars($tx->createdAt ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4"><a href="/admin/transactions" class="text-amber-600 hover:underline">View all transactions →</a></p>
        <?php endif; ?>
    </div>
</div>

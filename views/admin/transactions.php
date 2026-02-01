<div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Transaction Management</h1>
    <p class="text-slate-600 mb-6">Filter and view purchase transactions.</p>
    <form method="get" action="/admin/transactions" class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label for="product_id" class="block text-sm font-medium text-slate-700 mb-1">Product</label>
            <select id="product_id" name="product_id" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 min-w-[140px]">
                <option value="">All</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= (int) $p->id ?>" <?= $productId === $p->id ? 'selected' : '' ?>><?= htmlspecialchars($p->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="user_id" class="block text-sm font-medium text-slate-700 mb-1">User</label>
            <select id="user_id" name="user_id" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 min-w-[140px]">
                <option value="">All</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int) $u->id ?>" <?= $userId === $u->id ? 'selected' : '' ?>><?= htmlspecialchars($u->username) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="date_from" class="block text-sm font-medium text-slate-700 mb-1">From date</label>
            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom ?? '') ?>" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-slate-700 mb-1">To date</label>
            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo ?? '') ?>" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500">
        </div>
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition">Filter</button>
        <a href="/admin/transactions" class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium py-2 px-4 rounded-lg transition inline-block">Reset</a>
    </form>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">ID</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Product</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">User</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Qty</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Total (USD)</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $productMap = [];
                foreach ($products as $p) { $productMap[$p->id] = $p->name; }
                $userMap = [];
                foreach ($users as $u) { $userMap[$u->id] = $u->username; }
                ?>
                <?php foreach ($transactions as $tx): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-4 text-slate-700"><?= (int) $tx->id ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($productMap[$tx->productId] ?? '#' . $tx->productId) ?></td>
                    <td class="py-3 px-4 text-slate-700"><?= $tx->userId !== null ? htmlspecialchars($userMap[$tx->userId] ?? '#' . $tx->userId) : '—' ?></td>
                    <td class="py-3 px-4 text-slate-700"><?= (int) $tx->quantity ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($tx->totalAmount) ?></td>
                    <td class="py-3 px-4 text-slate-600 text-sm"><?= htmlspecialchars($tx->createdAt ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($transactions)): ?>
            <p class="p-6 text-slate-500">No transactions match your filters.</p>
        <?php endif; ?>
    </div>
    <div class="mt-4 flex items-center gap-4 text-sm text-slate-600">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?><?= $productId !== null ? '&product_id=' . $productId : '' ?><?= $userId !== null ? '&user_id=' . $userId : '' ?><?= $dateFrom ? '&date_from=' . urlencode($dateFrom) : '' ?><?= $dateTo ? '&date_to=' . urlencode($dateTo) : '' ?>" class="text-amber-600 hover:underline">Previous</a>
        <?php endif; ?>
        <span>Page <?= $page ?> of <?= max(1, $totalPages) ?> (<?= (int) $total ?> total)</span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?><?= $productId !== null ? '&product_id=' . $productId : '' ?><?= $userId !== null ? '&user_id=' . $userId : '' ?><?= $dateFrom ? '&date_from=' . urlencode($dateFrom) : '' ?><?= $dateTo ? '&date_to=' . urlencode($dateTo) : '' ?>" class="text-amber-600 hover:underline">Next</a>
        <?php endif; ?>
    </div>
</div>

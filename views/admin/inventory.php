<div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Inventory Tracking</h1>
    <p class="text-slate-600 mb-6">Monitor product stock levels. Items below threshold are highlighted.</p>
    <?php if ($lowStockCount > 0): ?>
        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800">
            <strong><?= (int) $lowStockCount ?></strong> product(s) have stock below <?= (int) $lowStockThreshold ?>.
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">ID</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Name</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Price (USD)</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Quantity</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <?php $isLow = $p->quantityAvailable < $lowStockThreshold; ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50 <?= $isLow ? 'bg-red-50' : '' ?>">
                    <td class="py-3 px-4 text-slate-700"><?= (int) $p->id ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($p->name) ?></td>
                    <td class="py-3 px-4 text-slate-700"><?= htmlspecialchars($p->price) ?></td>
                    <td class="py-3 px-4 font-medium <?= $isLow ? 'text-red-600' : 'text-slate-800' ?>"><?= (int) $p->quantityAvailable ?></td>
                    <td class="py-3 px-4">
                        <?php if ($isLow): ?>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700">Low stock</span>
                        <?php elseif ($p->quantityAvailable === 0): ?>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-slate-200 text-slate-700">Out of stock</span>
                        <?php else: ?>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-700">In stock</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4">
                        <a href="/products/<?= (int) $p->id ?>/edit" class="text-amber-600 hover:underline mr-2">Edit</a>
                        <a href="/products/<?= (int) $p->id ?>" class="text-green-600 hover:underline">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center gap-4 text-sm text-slate-600">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&sort=<?= htmlspecialchars($orderBy) ?>&dir=<?= htmlspecialchars($orderDir) ?>" class="text-amber-600 hover:underline">Previous</a>
        <?php endif; ?>
        <span>Page <?= $page ?> of <?= max(1, $totalPages) ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&sort=<?= htmlspecialchars($orderBy) ?>&dir=<?= htmlspecialchars($orderDir) ?>" class="text-amber-600 hover:underline">Next</a>
        <?php endif; ?>
    </div>
</div>

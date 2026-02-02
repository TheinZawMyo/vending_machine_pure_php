<?php
$pageTitle = 'Products';
if (!isset($user)) $user = null;
if (!isset($useAdminLayout)) $useAdminLayout = false;
if ($useAdminLayout) {
    $currentPage = 'products';
    require $basePath . '/layout/admin_header.php';
} else {
    require $basePath . '/layout/header.php';
}
?>
<?php if (!$useAdminLayout): ?>
<!-- User side: hero + product grid -->
<section class="mb-10">
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 mb-2">Choose your drink</h1>
    <p class="text-slate-500 text-lg">Fresh drinks, ready when you are.</p>
</section>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $p): ?>
    <a href="/products/<?= (int) $p->id ?>" class="group block bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden card-hover">
        <div class="p-6 sm:p-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition-transform">
                🥤
            </div>
            <h2 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-amber-600 transition-colors"><?= htmlspecialchars($p->name) ?></h2>
            <p class="text-2xl font-bold text-amber-600 mb-1">$<?= htmlspecialchars($p->price) ?></p>
            <p class="text-slate-500 text-sm"><?= (int) $p->quantityAvailable ?> in stock</p>
        </div>
        <div class="px-6 sm:px-8 pb-6">
            <span class="inline-flex items-center text-amber-600 font-semibold text-sm">
                View & purchase
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<div class="mt-8 flex items-center justify-center gap-4 text-sm text-slate-600">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>&sort=<?= htmlspecialchars($orderBy) ?>&dir=<?= htmlspecialchars($orderDir) ?>" class="btn-secondary">Previous</a>
    <?php endif; ?>
    <span class="font-medium">Page <?= $page ?> of <?= max(1, $totalPages) ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>&sort=<?= htmlspecialchars($orderBy) ?>&dir=<?= htmlspecialchars($orderDir) ?>" class="btn-primary">Next</a>
    <?php endif; ?>
</div>
<?php else: ?>
<!-- Admin side: table -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-slate-800">Products</h1>
        <?php if ($isAdmin): ?>
            <a href="/products/create" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition">Register Product</a>
        <?php endif; ?>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left py-3 px-4 font-semibold text-slate-700"><a href="?sort=id&dir=<?= $orderDir === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>&per_page=<?= $perPage ?>" class="hover:text-amber-600">ID</a></th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700"><a href="?sort=name&dir=<?= $orderDir === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>&per_page=<?= $perPage ?>" class="hover:text-amber-600">Name</a></th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700"><a href="?sort=price&dir=<?= $orderDir === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>&per_page=<?= $perPage ?>" class="hover:text-amber-600">Price (USD)</a></th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700"><a href="?sort=quantity&dir=<?= $orderDir === 'asc' ? 'desc' : 'asc' ?>&page=<?= $page ?>&per_page=<?= $perPage ?>" class="hover:text-amber-600">Available</a></th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-4 text-slate-700"><?= (int) $p->id ?></td>
                    <td class="py-3 px-4 font-medium text-slate-800"><?= htmlspecialchars($p->name) ?></td>
                    <td class="py-3 px-4 text-slate-700"><?= htmlspecialchars($p->price) ?></td>
                    <td class="py-3 px-4 text-slate-700"><?= (int) $p->quantityAvailable ?></td>
                    <td class="py-3 px-4">
                        <a href="/products/<?= (int) $p->id ?>" class="text-amber-600 hover:underline mr-2">View</a>
                        <?php if ($isAdmin): ?>
                            <a href="/products/<?= (int) $p->id ?>/edit" class="text-amber-600 hover:underline mr-2">Edit</a>
                            <a href="/products/<?= (int) $p->id ?>/delete" onclick="return confirm('Delete this product?')" class="text-red-600 hover:underline">Delete</a>
                        <?php endif; ?>
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
<?php endif; ?>
<?php
if ($useAdminLayout) {
    require $basePath . '/layout/admin_footer.php';
} else {
    require $basePath . '/layout/footer.php';
}
?>

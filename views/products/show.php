<?php
$pageTitle = 'Product: ' . htmlspecialchars($product->name);
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
<!-- User side: product detail card -->
<div class="max-w-xl mx-auto">
    <a href="/products" class="inline-flex items-center text-slate-500 hover:text-amber-600 text-sm font-medium mb-6 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to products
    </a>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-lg shadow-slate-200/50 overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center text-4xl mb-6">
                🥤
            </div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2"><?= htmlspecialchars($product->name) ?></h1>
            <p class="text-slate-500 text-sm mb-6"><?= (int) $product->quantityAvailable ?> in stock</p>
            <div class="flex items-baseline gap-2 mb-8">
                <span class="text-4xl font-bold text-amber-600">$<?= htmlspecialchars($product->price) ?></span>
                <span class="text-slate-500 text-sm">USD</span>
            </div>
            <?php if ($product->quantityAvailable > 0): ?>
                <a href="/products/<?= (int) $product->id ?>/purchase" class="btn-primary w-full sm:w-auto inline-flex justify-center text-base py-3.5 px-8">
                    Purchase now
                </a>
            <?php else: ?>
                <p class="text-slate-500 font-medium">Out of stock</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Admin side -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4"><?= htmlspecialchars($product->name) ?></h1>
    <p class="text-slate-700 mb-2"><strong>Price:</strong> <?= htmlspecialchars($product->price) ?> USD</p>
    <p class="text-slate-700 mb-4"><strong>Available:</strong> <?= (int) $product->quantityAvailable ?></p>
    <div class="flex flex-wrap gap-2">
        <a href="/products/<?= (int) $product->id ?>/purchase" class="inline-block font-medium py-2 px-4 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition">Purchase</a>
        <a href="/products/<?= (int) $product->id ?>/edit" class="inline-block font-medium py-2 px-4 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition">Edit</a>
        <a href="/products/<?= (int) $product->id ?>/delete" onclick="return confirm('Delete this product?')" class="inline-block font-medium py-2 px-4 rounded-xl bg-red-500 text-white hover:bg-red-600 transition">Delete</a>
        <a href="/products" class="text-slate-600 hover:underline py-2">Back to list</a>
        <a href="/admin/inventory" class="text-slate-600 hover:underline py-2">Inventory</a>
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

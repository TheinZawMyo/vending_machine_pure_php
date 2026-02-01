<?php
$pageTitle = 'Purchase: ' . htmlspecialchars($product->name);
if (!isset($user)) $user = null;
if (!isset($errors)) $errors = [];
if (!isset($success)) $success = false;
if (!isset($useAdminLayout)) $useAdminLayout = false;
if ($useAdminLayout) {
    $currentPage = 'products';
    require $basePath . '/layout/admin_header.php';
} else {
    require $basePath . '/layout/header.php';
}
?>
<?php if (!$useAdminLayout): ?>
<!-- User side: purchase flow -->
<div class="max-w-xl mx-auto">
    <a href="/products/<?= (int) $product->id ?>" class="inline-flex items-center text-slate-500 hover:text-amber-600 text-sm font-medium mb-6 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to <?= htmlspecialchars($product->name) ?>
    </a>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-lg shadow-slate-200/50 overflow-hidden">
        <div class="p-8 sm:p-10">
            <?php if ($success): ?>
                <div class="text-center py-4">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-3xl mx-auto mb-4">✓</div>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Purchase complete</h2>
                    <p class="text-slate-500 mb-6">Thank you for your order.</p>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="/products/<?= (int) $product->id ?>" class="btn-secondary">View product</a>
                        <a href="/products" class="btn-primary">Browse more</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center text-2xl">🥤</div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($product->name) ?></h1>
                        <p class="text-2xl font-bold text-amber-600">$<?= htmlspecialchars($product->price) ?> <span class="text-sm font-normal text-slate-500">each</span></p>
                        <p class="text-slate-500 text-sm"><?= (int) $product->quantityAvailable ?> available</p>
                    </div>
                </div>
                <form method="post" action="/products/<?= (int) $product->id ?>/purchase" id="purchase-form" class="space-y-5">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1.5">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="<?= (int) $product->quantityAvailable ?>" value="1" required class="input-field w-32">
                        <?php if (!empty($errors['quantity'])): ?>
                            <p class="mt-1.5 text-sm text-red-600"><?= htmlspecialchars($errors['quantity']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn-primary flex-1 sm:flex-none">Confirm purchase</button>
                        <a href="/products/<?= (int) $product->id ?>" class="btn-secondary">Cancel</a>
                    </div>
                </form>
                <script>
                document.getElementById('purchase-form').addEventListener('submit', function(e) {
                    var qty = parseInt(document.getElementById('quantity').value, 10);
                    var max = <?= (int) $product->quantityAvailable ?>;
                    if (isNaN(qty) || qty < 1) { e.preventDefault(); alert('Quantity must be at least 1.'); return; }
                    if (qty > max) { e.preventDefault(); alert('Quantity cannot exceed ' + max + '.'); return; }
                });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Admin side -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4">Purchase: <?= htmlspecialchars($product->name) ?></h1>
    <?php if ($success): ?>
        <p class="text-green-600 font-medium mb-4">Purchase completed successfully.</p>
        <div class="flex flex-wrap gap-2">
            <a href="/products/<?= (int) $product->id ?>" class="text-amber-600 hover:underline">View product</a>
            <a href="/products" class="text-amber-600 hover:underline">Products</a>
            <a href="/admin/inventory" class="text-amber-600 hover:underline">Inventory</a>
            <a href="/admin/transactions" class="text-amber-600 hover:underline">Transactions</a>
        </div>
    <?php else: ?>
        <p class="text-slate-700 mb-4"><strong>Price:</strong> <?= htmlspecialchars($product->price) ?> USD &nbsp;|&nbsp; <strong>Available:</strong> <?= (int) $product->quantityAvailable ?></p>
        <form method="post" action="/products/<?= (int) $product->id ?>/purchase" id="purchase-form" class="space-y-4 max-w-xs">
            <div>
                <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Quantity *</label>
                <input type="number" id="quantity" name="quantity" min="1" max="<?= (int) $product->quantityAvailable ?>" value="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                <?php if (!empty($errors['quantity'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['quantity']) ?></p>
                <?php endif; ?>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Purchase</button>
                <a href="/products/<?= (int) $product->id ?>" class="btn-secondary">Cancel</a>
                <a href="/admin/inventory" class="text-slate-600 hover:underline py-2">Back to Inventory</a>
            </div>
        </form>
        <script>
        document.getElementById('purchase-form').addEventListener('submit', function(e) {
            var qty = parseInt(document.getElementById('quantity').value, 10);
            var max = <?= (int) $product->quantityAvailable ?>;
            if (isNaN(qty) || qty < 1) { e.preventDefault(); alert('Quantity must be at least 1.'); return; }
            if (qty > max) { e.preventDefault(); alert('Quantity cannot exceed ' + max + '.'); return; }
        });
        </script>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php
if ($useAdminLayout) {
    require $basePath . '/layout/admin_footer.php';
} else {
    require $basePath . '/layout/footer.php';
}
?>

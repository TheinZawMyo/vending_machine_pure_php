<?php
$pageTitle = 'Edit Product: ' . htmlspecialchars($product->name);
if (!isset($user)) $user = null;
if (!isset($errors)) $errors = [];
$currentPage = 'products';
require $basePath . '/layout/admin_header.php';
?>
<div class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4">Edit Product</h1>
    <form method="post" action="/products/<?= (int) $product->id ?>/update" id="product-form" class="space-y-4 max-w-md">
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product->name ?? '') ?>" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <?php if (!empty($errors['name'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['name']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Price (USD) *</label>
            <input type="number" id="price" name="price" step="0.001" min="0.001" value="<?= htmlspecialchars($product->price ?? '') ?>" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <?php if (!empty($errors['price'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['price']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Quantity Available *</label>
            <input type="number" id="quantity" name="quantity" min="0" value="<?= (int) ($product->quantityAvailable ?? 0) ?>" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            <?php if (!empty($errors['quantity'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['quantity']) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition">Update Product</button>
            <a href="/products" class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium py-2 px-4 rounded-lg transition inline-block">Cancel</a>
            <a href="/admin/inventory" class="text-slate-600 hover:underline py-2">Back to Inventory</a>
        </div>
    </form>
</div>
<script>
document.getElementById('product-form').addEventListener('submit', function(e) {
    var name = document.getElementById('name').value.trim();
    var price = parseFloat(document.getElementById('price').value);
    var qty = parseInt(document.getElementById('quantity').value, 10);
    var err = [];
    if (!name) err.push('Name is required.');
    if (isNaN(price) || price <= 0) err.push('Price must be a positive number.');
    if (isNaN(qty) || qty < 0) err.push('Quantity must be non-negative.');
    if (err.length) { e.preventDefault(); alert(err.join(' ')); }
});
</script>
<?php require $basePath . '/layout/admin_footer.php'; ?>

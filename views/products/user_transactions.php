<?php
$pageTitle = 'My Transactions';
include __DIR__ . '/../layout/header.php';
?>
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold text-slate-800 mb-8">My Transactions</h1>

    <?php if (empty($transactions)): ?>
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🛒</div>
            <h2 class="text-xl font-semibold text-slate-700 mb-2">No transactions yet</h2>
            <p class="text-slate-500 mb-6">You haven't made any purchases. Start shopping!</p>
            <a href="/products" class="btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Product</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Quantity</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php
                        $productRepo = new \Theinzawmyo\VendingMachine\Repositories\ProductRepository();
                        foreach ($transactions as $transaction):
                            $product = $productRepo->find($transaction->productId);
                            $productName = $product ? htmlspecialchars($product->name) : 'Unknown Product';
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?= $transaction->createdAt ? date('M j, Y g:i A', strtotime($transaction->createdAt)) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                <?= $productName ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?= (int) $transaction->quantity ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-amber-600">
                                $<?= htmlspecialchars($transaction->totalAmount) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex items-center justify-center gap-4 text-sm text-slate-600">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?>" class="btn-secondary">Previous</a>
            <?php endif; ?>
            <span class="font-medium">Page <?= $page ?> of <?= max(1, $totalPages) ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?>" class="btn-primary">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
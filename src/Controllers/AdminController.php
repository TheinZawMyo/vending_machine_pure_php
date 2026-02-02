<?php

namespace Theinzawmyo\VendingMachine\Controllers;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Repositories\ProductRepository;
use Theinzawmyo\VendingMachine\Repositories\TransactionRepository;
use Theinzawmyo\VendingMachine\Repositories\UserRepository;

/**
 * Admin dashboard, inventory tracking, transaction management.
 */
class AdminController
{
    private ProductRepository $productRepository;
    private TransactionRepository $transactionRepository;
    private UserRepository $userRepository;
    private SessionAuth $auth;

    public function __construct(
        ?ProductRepository $productRepository = null,
        ?TransactionRepository $transactionRepository = null,
        ?UserRepository $userRepository = null,
        ?SessionAuth $auth = null
    ) {
        $this->productRepository = $productRepository ?? new ProductRepository();
        $this->transactionRepository = $transactionRepository ?? new TransactionRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->auth = $auth ?? new SessionAuth();
    }

    public function dashboard(): void
    {
        $totalProducts = $this->productRepository->count();
        $lowStockCount = $this->productRepository->countLowStock(10);
        $totalTransactions = $this->transactionRepository->countAll();
        $totalUsers = $this->userRepository->count();
        $recentTransactions = $this->transactionRepository->findAll(10, 0);
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $currentPage = 'dashboard';
        require $basePath . '/layout/admin_header.php';
        require $basePath . '/admin/dashboard.php';
        require $basePath . '/layout/admin_footer.php';
    }

    public function inventory(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;
        $orderBy = $_GET['sort'] ?? 'id';
        $orderDir = strtolower($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $products = $this->productRepository->findAll($perPage, $offset, $orderBy, $orderDir);
        $total = $this->productRepository->count();
        $totalPages = (int) ceil($total / $perPage);
        $lowStockThreshold = (int) ($_GET['threshold'] ?? 10);
        $lowStockCount = $this->productRepository->countLowStock($lowStockThreshold);
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $currentPage = 'inventory';
        require $basePath . '/layout/admin_header.php';
        require $basePath . '/admin/inventory.php';
        require $basePath . '/layout/admin_footer.php';
    }

    public function transactions(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;
        $productId = isset($_GET['product_id']) && $_GET['product_id'] !== '' ? (int) $_GET['product_id'] : null;
        $userId = isset($_GET['user_id']) && $_GET['user_id'] !== '' ? (int) $_GET['user_id'] : null;
        $dateFrom = isset($_GET['date_from']) && $_GET['date_from'] !== '' ? $_GET['date_from'] : null;
        $dateTo = isset($_GET['date_to']) && $_GET['date_to'] !== '' ? $_GET['date_to'] : null;

        $transactions = $this->transactionRepository->findAll($perPage, $offset, $productId, $userId, $dateFrom, $dateTo);
        $total = $this->transactionRepository->countAll($productId, $userId, $dateFrom, $dateTo);
        $totalPages = (int) ceil($total / $perPage);

        $products = $this->productRepository->findAll(500, 0, 'name', 'ASC');
        $users = $this->userRepository->findAll();

        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $currentPage = 'transactions';
        require $basePath . '/layout/admin_header.php';
        require $basePath . '/admin/transactions.php';
        require $basePath . '/layout/admin_footer.php';
    }
}

<?php

namespace Theinzawmyo\VendingMachine\Controllers;

use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Repositories\ProductRepository;
use Theinzawmyo\VendingMachine\Repositories\TransactionRepository;
use Theinzawmyo\VendingMachine\Validation\ProductValidator;
use Theinzawmyo\VendingMachine\Validation\PurchaseValidator;

/**
 * Handles CRUD for products and the purchase process.
 * Admin-only for create/update/delete; list and purchase available to users.
 */
class ProductsController
{
    private ProductRepository $productRepository;
    private TransactionRepository $transactionRepository;
    private SessionAuth $auth;
    private ProductValidator $productValidator;
    private PurchaseValidator $purchaseValidator;

    public function __construct(
        ?ProductRepository $productRepository = null,
        ?TransactionRepository $transactionRepository = null,
        ?SessionAuth $auth = null,
        ?ProductValidator $productValidator = null,
        ?PurchaseValidator $purchaseValidator = null
    ) {
        $this->productRepository = $productRepository ?? new ProductRepository();
        $this->transactionRepository = $transactionRepository ?? new TransactionRepository();
        $this->auth = $auth ?? new SessionAuth();
        $this->productValidator = $productValidator ?? new ProductValidator();
        $this->purchaseValidator = $purchaseValidator ?? new PurchaseValidator();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 12)));
        $orderBy = $_GET['sort'] ?? 'id';
        $orderDir = strtolower($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $offset = ($page - 1) * $perPage;

        $products = $this->productRepository->findAll($perPage, $offset, $orderBy, $orderDir);
        $total = $this->productRepository->count();
        $totalPages = (int) ceil($total / $perPage);

        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $isAdmin = $user && $user->isAdmin();
        $useAdminLayout = $isAdmin;
        require $basePath . '/products/index.php';
    }

    public function show(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $isAdmin = $user && $user->isAdmin();
        $useAdminLayout = $isAdmin;
        require $basePath . '/products/show.php';
    }

    /**
     * Purchase action: update product quantity and log transaction.
     * GET: show purchase form; POST: validate and process.
     */
    public function purchase(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            $isAdmin = $user && $user->isAdmin();
            $useAdminLayout = $isAdmin;
            $errors = [];
            require $basePath . '/products/purchase.php';
            return;
        }

        $data = array_merge($_POST, $_GET);
        $quantity = (int) ($data['quantity'] ?? 0);
        if (!$this->purchaseValidator->validate($data, $product->quantityAvailable)) {
            $errors = $this->purchaseValidator->getErrors();
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            $isAdmin = $user && $user->isAdmin();
            $useAdminLayout = $isAdmin;
            require $basePath . '/products/purchase.php';
            return;
        }

        $quantity = (int) $data['quantity'];
        $total = bcmul((string) $product->price, (string) $quantity, 3);
        $userId = $this->auth->user()?->id ?? null;

        $pdo = $this->productRepository->getConnection();
        $pdo->beginTransaction();
        try {
            $ok = $this->productRepository->decrementQuantity($product->id, $quantity);
            if (!$ok) {
                $pdo->rollBack();
                $errors = ['quantity' => 'Insufficient stock.'];
                $basePath = __DIR__ . '/../../views';
                $user = $this->auth->user();
                $isAdmin = $user && $user->isAdmin();
                $useAdminLayout = $isAdmin;
                require $basePath . '/products/purchase.php';
                return;
            }
            $this->transactionRepository->create($product->id, $userId, $quantity, $total);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        $product = $this->productRepository->find($id);
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        $isAdmin = $user && $user->isAdmin();
        $useAdminLayout = $isAdmin;
        $success = true;
        require $basePath . '/products/purchase.php';
    }

    public function createForm(): void
    {
        $product = null;
        $errors = [];
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        require $basePath . '/products/create.php';
    }

    public function create(): void
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? '',
            'quantity' => $_POST['quantity'] ?? 0,
        ];
        if (!$this->productValidator->validate($data)) {
            $errors = $this->productValidator->getErrors();
            $product = new \Theinzawmyo\VendingMachine\Models\Product();
            $product->id = 0;
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->quantityAvailable = (int) $data['quantity'];
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            require $basePath . '/products/create.php';
            return;
        }
        $this->productRepository->create($data);
        header('Location: /admin/inventory');
        return;
    }

    public function editForm(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }
        $errors = [];
        $basePath = __DIR__ . '/../../views';
        $user = $this->auth->user();
        require $basePath . '/products/edit.php';
    }

    public function update(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }
        $data = [
            'name' => $_POST['name'] ?? '',
            'price' => $_POST['price'] ?? '',
            'quantity' => $_POST['quantity'] ?? 0,
        ];
        if (!$this->productValidator->validate($data)) {
            $errors = $this->productValidator->getErrors();
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->quantityAvailable = (int) $data['quantity'];
            $basePath = __DIR__ . '/../../views';
            $user = $this->auth->user();
            require $basePath . '/products/edit.php';
            return;
        }
        $this->productRepository->update($id, $data);
        header('Location: /admin/inventory');
        return;
    }

    public function delete(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }
        $this->productRepository->delete($id);
        header('Location: /products');
        return;
    }

    public function userTransactions(): void
    {
        $user = $this->auth->user();
        if (!$user) {
            http_response_code(403);
            echo 'Forbidden.';
            return;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;

        $transactions = $this->transactionRepository->findByUserId($user->id, $perPage, $offset);
        $total = $this->transactionRepository->countByUserId($user->id);
        $totalPages = (int) ceil($total / $perPage);

        $basePath = __DIR__ . '/../../views';
        require $basePath . '/products/user_transactions.php';
    }
}

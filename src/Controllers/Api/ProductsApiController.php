<?php

namespace Theinzawmyo\VendingMachine\Controllers\Api;

use Theinzawmyo\VendingMachine\Auth\JwtAuth;
use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Repositories\ProductRepository;
use Theinzawmyo\VendingMachine\Repositories\TransactionRepository;
use Theinzawmyo\VendingMachine\Validation\ProductValidator;
use Theinzawmyo\VendingMachine\Validation\PurchaseValidator;
use Theinzawmyo\VendingMachine\Core\Logger;

/**
 * RESTful API for products and purchase.
 * Uses JWT for authentication; admin-only for create/update/delete.
 */
class ProductsApiController
{
    private ProductRepository $productRepository;
    private TransactionRepository $transactionRepository;
    private JwtAuth $jwtAuth;
    private ProductValidator $productValidator;
    private PurchaseValidator $purchaseValidator;
    private Logger $logger;

    public function __construct(
        ?ProductRepository $productRepository = null,
        ?TransactionRepository $transactionRepository = null,
        ?JwtAuth $jwtAuth = null,
        ?ProductValidator $productValidator = null,
        ?PurchaseValidator $purchaseValidator = null,
        ?Logger $logger = null
    ) {
        $this->productRepository = $productRepository ?? new ProductRepository();
        $this->transactionRepository = $transactionRepository ?? new TransactionRepository();
        $this->jwtAuth = $jwtAuth ?? new JwtAuth();
        $this->productValidator = $productValidator ?? new ProductValidator();
        $this->purchaseValidator = $purchaseValidator ?? new PurchaseValidator();
        $this->logger = $logger ?? new Logger();
    }

    private function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    private function requireAdmin()
    {
        $user = $this->jwtAuth->userFromRequest();
        if ($user === null) {
            $this->json(['error' => 'Unauthorized'], 401);
            exit;
        }
        if (!$user->isAdmin()) {
            $this->json(['error' => 'Forbidden: Admin required'], 403);
            exit;
        }
    }

    private function requireAuth(): ?\Theinzawmyo\VendingMachine\Models\User
    {
        $user = $this->jwtAuth->userFromRequest();
        if ($user === null) {
            $this->json(['error' => 'Unauthorized'], 401);
            exit;
        }
        return $user;
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 10)));
        $orderBy = $_GET['sort'] ?? 'id';
        $orderDir = strtolower($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $offset = ($page - 1) * $perPage;
        $products = $this->productRepository->findAll($perPage, $offset, $orderBy, $orderDir);
        $total = $this->productRepository->count();
        $this->json([
            'data' => array_map(fn($p) => $p->toArray(), $products),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function show(int $id): void
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }
        $this->json($product->toArray());
    }

    // public function create(): void
    // {
    //     $this->requireAdmin();
    //     $input = json_decode(file_get_contents('php://input'), true) ?: [];
    //     $data = [
    //         'name' => $input['name'] ?? '',
    //         'price' => $input['price'] ?? '',
    //         'quantity' => $input['quantity'] ?? 0,
    //     ];
    //     if (!$this->productValidator->validate($data)) {
    //         $this->json(['error' => 'Validation failed', 'errors' => $this->productValidator->getErrors()], 422);
    //         return;
    //     }
    //     $product = $this->productRepository->create($data);
    //     $this->json($product->toArray(), 201);
    // }

    // public function update(int $id): void
    // {
    //     $this->requireAdmin();
    //     $product = $this->productRepository->find($id);
    //     if (!$product) {
    //         $this->json(['error' => 'Product not found'], 404);
    //         return;
    //     }
    //     $input = json_decode(file_get_contents('php://input'), true) ?: [];
    //     $data = [
    //         'name' => $input['name'] ?? $product->name,
    //         'price' => $input['price'] ?? $product->price,
    //         'quantity' => array_key_exists('quantity', $input) ? $input['quantity'] : $product->quantityAvailable,
    //     ];
    //     if (!$this->productValidator->validate($data)) {
    //         $this->json(['error' => 'Validation failed', 'errors' => $this->productValidator->getErrors()], 422);
    //         return;
    //     }
    //     $product = $this->productRepository->update($id, $data);
    //     $this->json($product->toArray());
    // }

    // public function delete(int $id): void
    // {
    //     $this->requireAdmin();
    //     $product = $this->productRepository->find($id);
    //     if (!$product) {
    //         $this->json(['error' => 'Product not found'], 404);
    //         return;
    //     }
    //     $this->productRepository->delete($id);
    //     $this->json(['message' => 'Deleted'], 200);
    // }

    /**
     * Purchase: requires auth (JWT), updates quantity and logs transaction.
     */
    public function purchase(int $id): void
    {
        $this->requireAuth();
        $product = $this->productRepository->find($id);
        $this->logger->info("Purchase attempt for product ID {$id}");
        if (!$product) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $data = ['quantity' => $input['quantity'] ?? 0];
        if (!$this->purchaseValidator->validate($data, $product->quantityAvailable)) {
            $this->json(['error' => 'Validation failed', 'errors' => $this->purchaseValidator->getErrors()], 422);
            return;
        }
        $quantity = (int) $data['quantity'];
        $total = bcmul((string) $product->price, (string) $quantity, 3);
        $user = $this->jwtAuth->userFromRequest();
        $pdo = $this->productRepository->getConnection();
        $pdo->beginTransaction();
        try {
            $ok = $this->productRepository->decrementQuantity($product->id, $quantity);
            if (!$ok) {
                $pdo->rollBack();
                $this->json(['error' => 'Insufficient stock'], 422);
                return;
            }
            $this->transactionRepository->create($product->id, $user->id, $quantity, $total);
            $pdo->commit();
        } catch (\Throwable $e) {
            $this->logger->error("Purchase failed for product ID {$id}: " . $e->getMessage());
            $pdo->rollBack();
            throw $e;
        }
        $this->json(['message' => 'Purchase completed', 'total' => $total], 200);
    }
}

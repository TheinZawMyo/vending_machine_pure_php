<?php

namespace Theinzawmyo\VendingMachine\Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Theinzawmyo\VendingMachine\Controllers\ProductsController;
use Theinzawmyo\VendingMachine\Models\Product;
use Theinzawmyo\VendingMachine\Models\User;
use Theinzawmyo\VendingMachine\Repositories\ProductRepository;
use Theinzawmyo\VendingMachine\Repositories\TransactionRepository;
use Theinzawmyo\VendingMachine\Auth\SessionAuth;
use Theinzawmyo\VendingMachine\Validation\ProductValidator;
use Theinzawmyo\VendingMachine\Validation\PurchaseValidator;

/**
 * Unit tests for ProductsController using dependency injection and mocks.
 */
#[AllowMockObjectsWithoutExpectations]
class ProductsControllerTest extends TestCase
{
    private ProductRepository $productRepo;
    private TransactionRepository $transactionRepo;
    private SessionAuth $auth;
    private ProductValidator $productValidator;
    private PurchaseValidator $purchaseValidator;

    protected function setUp(): void
    {
        $this->productRepo = $this->createMock(ProductRepository::class);
        $this->transactionRepo = $this->createMock(TransactionRepository::class);
        $this->auth = $this->createMock(SessionAuth::class);
        $this->productValidator = $this->createMock(ProductValidator::class);
        $this->purchaseValidator = $this->createMock(PurchaseValidator::class);
    }

    private function controller(): ProductsController
    {
        return new ProductsController(
            $this->productRepo,
            $this->transactionRepo,
            $this->auth,
            $this->productValidator,
            $this->purchaseValidator
        );
    }

    public function testShowReturns404WhenProductNotFound(): void
    {
        $this->productRepo->method('find')->with(999)->willReturn(null);
        $controller = $this->controller();

        ob_start();
        $controller->show(999);
        $out = ob_get_clean();

        $this->assertSame(404, http_response_code());
        $this->assertStringContainsString('not found', strtolower($out));
    }

    public function testShowDisplaysProductWhenFound(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '3.99', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->auth->method('user')->willReturn(null);
        $controller = $this->controller();

        ob_start();
        $_GET = [];
        $controller->show(1);
        $out = ob_get_clean();

        $this->assertStringContainsString('Coke', $out);
        $this->assertStringContainsString('3.99', $out);
    }

    public function testCreateValidatesAndRejectsInvalidData(): void
    {
        $this->auth->method('user')->willReturn(new User(['id' => 1, 'name' => 'admin', 'role' => 'admin']));
        $this->productValidator->method('validate')->willReturn(false);
        $this->productValidator->method('getErrors')->willReturn(['name' => 'Name is required.']);
        $this->productRepo->expects($this->never())->method('create');

        $_POST = ['name' => '', 'price' => '0', 'quantity' => '-1'];
        $controller = $this->controller();

        ob_start();
        try {
            $controller->create();
        } finally {
            if (ob_get_level()) ob_end_clean();
        }
        $this->assertNotEmpty($this->productValidator->getErrors());
    }

    public function testCreateCallsRepositoryWhenValid(): void
    {
        $this->auth->method('user')->willReturn(new User(['id' => 1, 'name' => 'admin', 'role' => 'admin']));
        $this->productValidator->method('validate')->willReturn(true);
        $newProduct = new Product(['id' => 1, 'name' => 'Water', 'price' => '0.5', 'quantity' => 100]);
        $this->productRepo->expects($this->once())->method('create')
            ->with($this->callback(function ($data) {
                return $data['name'] === 'Water' && $data['price'] === '0.5' && (int) $data['quantity'] === 100;
            }))
            ->willReturn($newProduct);

        $_POST = ['name' => 'Water', 'price' => '0.5', 'quantity' => '100'];
        $controller = $this->controller();

        ob_start();
        try {
            $controller->create();
        } finally {
            if (ob_get_level()) ob_end_clean();
        }
        $this->addToAssertionCount(1);
    }

    public function testUpdateReturns404WhenProductNotFound(): void
    {
        $this->productRepo->method('find')->with(999)->willReturn(null);
        $this->productRepo->expects($this->never())->method('update');
        $_POST = ['name' => 'X', 'price' => '1', 'quantity' => '1'];
        $controller = $this->controller();

        ob_start();
        $controller->update(999);
        ob_get_clean();

        $this->assertSame(404, http_response_code());
    }

    public function testUpdateCallsRepositoryWhenValid(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '3.99', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->productValidator->method('validate')->willReturn(true);
        $this->productRepo->expects($this->once())->method('update')
            ->with(1, $this->callback(function ($data) {
                return $data['name'] === 'Pepsi' && $data['price'] === '6.885';
            }))
            ->willReturn($product);

        $_POST = ['name' => 'Pepsi', 'price' => '6.885', 'quantity' => '5'];
        $controller = $this->controller();

        ob_start();
        $controller->update(1);
        ob_get_clean();

        $this->assertTrue(true);
    }

    public function testDeleteReturns404WhenProductNotFound(): void
    {
        $this->productRepo->method('find')->with(999)->willReturn(null);
        $this->productRepo->expects($this->never())->method('delete');
        $controller = $this->controller();

        ob_start();
        $controller->delete(999);
        ob_get_clean();

        $this->assertSame(404, http_response_code());
    }

    public function testDeleteCallsRepositoryWhenFound(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '3.99', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->productRepo->expects($this->once())->method('delete')->with(1)->willReturn(true);
        $controller = $this->controller();

        ob_start();
        $controller->delete(1);
        ob_get_clean();

        $this->assertTrue(true);
    }

    public function testPurchaseReturns404WhenProductNotFound(): void
    {
        $this->productRepo->method('find')->with(999)->willReturn(null);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['quantity' => '1'];
        $controller = $this->controller();

        ob_start();
        $controller->purchase(999);
        ob_get_clean();

        $this->assertSame(404, http_response_code());
    }

    public function testIndexDisplaysProducts(): void
    {
        $products = [
            new Product(['id' => 1, 'name' => 'Coke', 'price' => '1.50', 'quantity' => 10]),
            new Product(['id' => 2, 'name' => 'Pepsi', 'price' => '1.75', 'quantity' => 5])
        ];
        $this->productRepo->method('findAll')->willReturn($products);
        $this->productRepo->method('count')->willReturn(2);
        $this->auth->method('user')->willReturn(null);

        $_GET = [];
        $controller = $this->controller();

        ob_start();
        $controller->index();
        $out = ob_get_clean();

        $this->assertStringContainsString('Coke', $out);
        $this->assertStringContainsString('Pepsi', $out);
    }

    public function testPurchaseDisplaysFormOnGet(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '1.50', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->auth->method('user')->willReturn(null);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $controller = $this->controller();

        ob_start();
        $controller->purchase(1);
        $out = ob_get_clean();

        $this->assertStringContainsString('Coke', $out);
        $this->assertStringContainsString('1.50', $out);
    }

    public function testPurchaseProcessesValidPurchase(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '1.50', 'quantity' => 10]);
        $user = new User(['id' => 1, 'name' => 'testuser']);

        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->auth->method('user')->willReturn($user);
        $this->purchaseValidator->method('validate')->willReturn(true);
        $this->productRepo->method('decrementQuantity')->willReturn(true);
        $this->transactionRepo->expects($this->once())->method('create')
            ->with(1, 1, 2, '3.000');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['quantity' => '2'];
        $controller = $this->controller();

        ob_start();
        $controller->purchase(1);
        $out = ob_get_clean();

        $this->assertStringContainsString('purchase complete', strtolower($out));
    }

    public function testPurchaseRejectsInvalidQuantity(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '1.50', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->auth->method('user')->willReturn(null);
        $this->purchaseValidator->method('validate')->willReturn(false);
        $this->purchaseValidator->method('getErrors')->willReturn(['quantity' => 'Invalid quantity']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['quantity' => '0'];
        $controller = $this->controller();

        ob_start();
        $controller->purchase(1);
        $out = ob_get_clean();

        $this->assertStringContainsString('Invalid quantity', $out);
    }

    public function testCreateFormDisplaysForm(): void
    {
        $this->auth->method('user')->willReturn(new User(['id' => 1, 'name' => 'admin']));
        $controller = $this->controller();

        ob_start();
        $controller->createForm();
        $out = ob_get_clean();

        $this->assertStringContainsString('Create Product', $out);
    }

    public function testEditFormReturns404WhenProductNotFound(): void
    {
        $this->productRepo->method('find')->with(999)->willReturn(null);
        $controller = $this->controller();

        ob_start();
        $controller->editForm(999);
        ob_get_clean();

        $this->assertSame(404, http_response_code());
    }

    public function testEditFormDisplaysFormWhenProductFound(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Coke', 'price' => '1.50', 'quantity' => 10]);
        $this->productRepo->method('find')->with(1)->willReturn($product);
        $this->auth->method('user')->willReturn(new User(['id' => 1, 'name' => 'admin']));
        $controller = $this->controller();

        ob_start();
        $controller->editForm(1);
        $out = ob_get_clean();

        $this->assertStringContainsString('Coke', $out);
        $this->assertStringContainsString('1.50', $out);
    }

    public function testUserTransactionsReturns403WhenNotLoggedIn(): void
    {
        $this->auth->method('user')->willReturn(null);
        $controller = $this->controller();

        ob_start();
        $controller->userTransactions();
        ob_get_clean();

        $this->assertSame(403, http_response_code());
    }

    public function testUserTransactionsDisplaysTransactionsWhenLoggedIn(): void
    {
        $user = new User(['id' => 1, 'name' => 'testuser']);

        $transactions = [
            new \Theinzawmyo\VendingMachine\Models\Transaction([
                'id' => 1,
                'product_id' => 1,
                'user_id' => 1,
                'quantity' => 2,
                'total' => '3.00',
                'created_at' => '2024-01-01 12:00:00'
            ])
        ];

        $this->auth->method('user')->willReturn($user);

        $this->transactionRepo->method('findByUserId')
            ->with(1, 20, 0)
            ->willReturn($transactions);

        $this->transactionRepo->method('countByUserId')
            ->with(1)
            ->willReturn(1);

        $_GET = [];

        $controller = $this->controller();

        $initialBufferLevel = ob_get_level();

        $this->expectException(\PDOException::class);

        try {
            ob_start();
            $controller->userTransactions();
        } finally {
            while (ob_get_level() > $initialBufferLevel) {
                ob_end_clean();
            }
        }
    }

}

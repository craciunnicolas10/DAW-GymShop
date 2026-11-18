<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Validator;
use App\Exceptions\AuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\Cart;

/**
 * Checkout and order history. All actions require an authenticated user.
 */
final class OrderController extends Controller
{
    private Cart $cart;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->requireLogin();

        $pdo = Database::getConnection();
        $this->cart = Cart::fromSession();
        $this->orderRepository = new OrderRepository($pdo);
        $this->productRepository = new ProductRepository($pdo);
    }

    public function checkout(): void
    {
        if ($this->cart->isEmpty()) {
            $this->session->flash('info', 'Coșul este gol. Adaugă produse înainte de a finaliza comanda.');
            $this->redirect('/cart');
        }

        $this->renderCheckout([], []);
    }

    public function placeOrder(): void
    {
        $data = $this->request->all();
        $lines = $this->cart->getLines($this->productRepository->findByIds($this->cart->getProductIds()));

        if ($lines === []) {
            $this->redirect('/cart');
        }

        try {
            (new Validator($data))
                ->required('shippingAddress', 'Adresă de livrare')
                ->minLength('shippingAddress', 'Adresă de livrare', 10)
                ->maxLength('shippingAddress', 'Adresă de livrare', 255)
                ->required('phone', 'Telefon')
                ->pattern('phone', '/^(\+4)?07\d{8}$/', 'Introdu un număr de telefon mobil valid (ex. 0712345678).')
                ->validate();
        } catch (ValidationException $exception) {
            $this->renderCheckout($exception->getErrors(), $data, 422);
            return;
        }

        $orderLines = array_map(static fn (array $line): array => [
            'productId' => $line['product']->getId(),
            'productName' => $line['product']->getName(),
            'quantity' => $line['quantity'],
            'unitPrice' => $line['product']->getPrice(),
        ], $lines);

        try {
            $orderId = $this->orderRepository->createOrder(
                $this->auth->id(),
                $data['shippingAddress'],
                $data['phone'],
                $orderLines
            );
        } catch (ValidationException $exception) {
            // Another customer bought the last items in the meantime.
            $this->session->flash('error', implode(' ', $exception->getErrors()));
            $this->redirect('/cart');
        }

        $this->cart->clear();
        $this->session->flash('success', "Comanda #{$orderId} a fost plasată cu succes. Îți mulțumim!");
        $this->redirect('/orders/' . $orderId);
    }

    public function index(): void
    {
        $this->render('orders/index', [
            'pageTitle' => 'Comenzile mele',
            'orders' => $this->orderRepository->findByUserId($this->auth->id()),
        ]);
    }

    public function show(string $id): void
    {
        $order = $this->orderRepository->findById((int) $id);

        if ($order === null) {
            throw new NotFoundException('Comanda nu există.');
        }

        // Authorization: a customer can see only his own orders, the admin can see all of them.
        if (!$order->belongsTo($this->auth->id()) && !$this->auth->isAdmin()) {
            throw new AuthorizationException('Nu ai acces la această comandă.');
        }

        $this->render('orders/show', [
            'pageTitle' => 'Comanda #' . $order->getId(),
            'order' => $order,
        ]);
    }

    private function renderCheckout(array $errors, array $old, int $statusCode = 200): void
    {
        $lines = $this->cart->getLines($this->productRepository->findByIds($this->cart->getProductIds()));

        $this->render('orders/checkout', [
            'pageTitle' => 'Finalizare comandă',
            'lines' => $lines,
            'total' => array_sum(array_column($lines, 'subtotal')),
            'errors' => $errors,
            'old' => $old,
        ], $statusCode);
    }
}

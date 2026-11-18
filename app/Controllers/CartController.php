<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Repositories\ProductRepository;
use App\Services\Cart;
use InvalidArgumentException;

final class CartController extends Controller
{
    private Cart $cart;
    private ProductRepository $productRepository;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->cart = Cart::fromSession();
        $this->productRepository = new ProductRepository(Database::getConnection());
    }

    public function index(): void
    {
        $products = $this->productRepository->findByIds($this->cart->getProductIds());
        $lines = $this->cart->getLines($products);

        $this->render('cart/index', [
            'pageTitle' => 'Coșul meu',
            'lines' => $lines,
            'total' => array_sum(array_column($lines, 'subtotal')),
        ]);
    }

    public function add(): void
    {
        $product = $this->productRepository->findById((int) $this->request->input('productId'));

        if ($product === null) {
            throw new NotFoundException('Produsul nu există.');
        }

        try {
            $quantity = (int) $this->request->input('quantity', '1');
            $newQuantity = $this->cart->add($product->getId(), $quantity, $product->getStock());
            $this->logActivity('CART_ADD', ['productId' => $product->getId(), 'quantity' => $quantity, 'cartQuantity' => $newQuantity]);
            $this->session->flash('success', "„{$product->getName()}” a fost adăugat în coș.");
        } catch (InvalidArgumentException $exception) {
            $this->session->flash('error', $exception->getMessage());
        }

        $this->redirect('/cart');
    }

    public function update(): void
    {
        $productId = (int) $this->request->input('productId');
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            $this->cart->remove($productId);
        } else {
            $this->cart->update($productId, (int) $this->request->input('quantity'), $product->getStock());
        }

        $this->logActivity('CART_UPDATE', ['productId' => $productId, 'quantity' => $this->cart->getQuantity($productId)]);
        $this->session->flash('success', 'Coșul a fost actualizat.');
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $productId = (int) $this->request->input('productId');
        $this->cart->remove($productId);
        $this->logActivity('CART_REMOVE', ['productId' => $productId]);
        $this->session->flash('success', 'Produsul a fost eliminat din coș.');
        $this->redirect('/cart');
    }
}

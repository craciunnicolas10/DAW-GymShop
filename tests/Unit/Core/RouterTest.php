<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Router;
use App\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->get('/products', ['ProductController', 'index']);
        $this->router->get('/products/{slug}', ['ProductController', 'show']);
        $this->router->post('/cart/add', ['CartController', 'add']);
        $this->router->get('/admin/products/{id}/edit', ['AdminProductController', 'edit']);
    }

    public function testMatchesStaticRoute(): void
    {
        $route = $this->router->match('GET', '/products');

        $this->assertSame(['ProductController', 'index'], $route['handler']);
        $this->assertSame([], $route['params']);
    }

    public function testExtractsRouteParameters(): void
    {
        $route = $this->router->match('GET', '/products/creatina-monohidrat-300g');

        $this->assertSame(['ProductController', 'show'], $route['handler']);
        $this->assertSame(['slug' => 'creatina-monohidrat-300g'], $route['params']);
    }

    public function testExtractsParameterInTheMiddleOfThePath(): void
    {
        $route = $this->router->match('GET', '/admin/products/7/edit');

        $this->assertSame(['id' => '7'], $route['params']);
    }

    public function testMethodIsCaseInsensitive(): void
    {
        $route = $this->router->match('post', '/cart/add');

        $this->assertSame(['CartController', 'add'], $route['handler']);
    }

    public function testThrowsNotFoundForUnknownPath(): void
    {
        $this->expectException(NotFoundException::class);

        $this->router->match('GET', '/does-not-exist');
    }

    public function testThrowsNotFoundWhenMethodDoesNotMatch(): void
    {
        $this->expectException(NotFoundException::class);

        $this->router->match('GET', '/cart/add');
    }

    public function testParameterDoesNotMatchMultipleSegments(): void
    {
        $this->expectException(NotFoundException::class);

        $this->router->match('GET', '/products/creatina/extra');
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

final class HomeController extends Controller
{
    public function index(): void
    {
        $pdo = Database::getConnection();
        $productRepository = new ProductRepository($pdo);
        $categoryRepository = new CategoryRepository($pdo);

        $this->render('home/index', [
            'pageTitle' => 'Suplimente pentru sală',
            'categories' => $categoryRepository->findAll(),
            'featuredProducts' => $productRepository->findFeatured(8),
        ]);
    }
}

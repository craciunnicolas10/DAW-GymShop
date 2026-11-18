<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

/**
 * Static content pages.
 */
final class PageController extends Controller
{
    public function about(): void
    {
        $this->render('pages/about', [
            'pageTitle' => 'Ghid suplimente',
            'metaDescription' => 'Ghid GymShop: cum se folosesc creatina, proteina și pre-workout-ul, doze recomandate și surse științifice.',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ImageRepo;

class HomeController extends Controller
{
    public function index(): void
    {
        $images = ImageRepo::getAllWithAuthor();
        $this->render('home', [
            'title' => 'Galerie',
            'images' => $images,
        ]);
    }
}

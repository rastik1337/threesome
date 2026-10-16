<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $this->render('profile', [
            'title' => 'Můj profil',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home', ['title' => 'Home']);
    }

    public function pageTwo(): void
    {
        $this->render('page-two', ['title' => 'Page Two']);
    }

    public function pageThree(): void
    {
        $this->render('page-three', ['title' => 'Page Three']);
    }
}

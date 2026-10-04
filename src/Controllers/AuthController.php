<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->render('login', ['title' => 'Přihlášení']);
    }

    public function register(): void
    {
        $this->render('register', ['title' => 'Registrace']);
    }

    public function handleLogin(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($login === '') {
            $errors[] = 'Vyplňte přihlašovací jméno.';
        }
        if ($password === '') {
            $errors[] = 'Vyplňte heslo.';
        }

        $this->render('login', [
            'title' => 'Přihlášení',
            'errors' => $errors,
            'old' => ['login' => $login],
        ]);
    }

    public function handleRegister(): void
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if ($firstName === '') {
            $errors[] = 'Vyplňte jméno.';
        }
        if ($lastName === '') {
            $errors[] = 'Vyplňte příjmení.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Zadejte platný formát e-mailu.';
        }
        if ($phone === '' || !preg_match('/^(\+420|\+421)?\s?([1-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3})$/', $phone)) {
            $errors[] = 'Zadejte platné české nebo slovenské telefonní číslo.';
        }
        if (!in_array($gender, ['male', 'female', 'other'], true)) {
            $errors[] = 'Vyberte pohlaví.';
        }
        if ($login === '' || strlen($login) < 3 || !preg_match('/^[a-zA-Z0-9_-]+$/', $login)) {
            $errors[] = 'Uživatelské jméno musí mít alespoň 3 znaky.';
        }
        if ($password === '' || strlen($password) < 6) {
            $errors[] = 'Heslo musí mít alespoň 6 znaků.';
        }
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Vyberte profilovou fotografii.';
        }

        $this->render('register', [
            'title' => 'Registrace',
            'errors' => $errors,
            'old' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'login' => $login,
            ],
        ]);
    }
}

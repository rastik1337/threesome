<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Encryption;
use App\Models\UserRepo;
use Exception;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->requireGuest();
        $this->render('login', ['title' => 'Přihlášení']);
    }

    public function register(): void
    {
        $this->requireGuest();
        $this->render('register', ['title' => 'Registrace']);
    }

    public function handleLogin(): void
    {
        $this->requireGuest();
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($login === '') {
            $errors[] = 'Vyplňte přihlašovací jméno.';
        }
        if ($password === '') {
            $errors[] = 'Vyplňte heslo.';
        }

        $user = UserRepo::getByLogin($login);
        if (!$user) {
            $errors[] = "Špatné údaje";
        }
        if (!password_verify($password, $user->password)) {
            $errors[] = "Špatné údaje";
        }

        if (!empty($errors)) {
            $this->render('login', [
                'title' => 'Přihlášení',
                'errors' => $errors,
                'old' => ['login' => $login],
            ]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user->user_id,
            'login' => $user->login,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender->translate(),
            'role' => $user->role->translate(),
        ];
        $_SESSION['last_activity'] = time();

        $this->setNotification('success', 'Byli jste úspěšně přihlášeni.');
        $this->redirect('/');
    }

    public function handleRegister(): void
    {
        $this->requireGuest();
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

        // TODO: Register user here. Unique login, save profile picture, encrypt
        // GDPR-sensitive fields (add secret key for that) and hash password.
        try {
            // TODO: Move encryption and hashing to repository?
            UserRepo::create([
                'login' => $login,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => Encryption::encrypt($firstName),
                'last_name' => Encryption::encrypt($lastName),
                'email' => Encryption::encrypt($email),
                'phone' => Encryption::encrypt($phone),
                'gender' => $gender,
            ]);
        } catch (Exception $e) {
            $errors[] = "Nastala chyba při vytváření uživatele: " . $e->getMessage();
        }

        if (!empty($errors)) {
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
            return;
        }

        $this->setNotification('success', 'Registrace proběhla úspěšně. Nyní se můžete přihlásit.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->requireAuth();
        unset($_SESSION['user'], $_SESSION['last_activity']);
        session_regenerate_id(true);
        $this->setNotification('success', 'Byli jste úspěšně odhlášeni.');
        $this->redirect('/login');
    }
}

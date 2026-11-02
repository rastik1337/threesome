<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\GalleryController;
use App\Controllers\MailboxController;

Router::get('/', [HomeController::class, 'index']);
Router::get('/profile', [ProfileController::class, 'index']);

Router::post('/gallery/upload', [GalleryController::class, 'handleUpload']);
Router::post('/gallery/delete', [GalleryController::class, 'delete']);

Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'handleLogin']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'handleRegister']);
Router::get('/logout', [AuthController::class, 'logout']);
Router::post('/logout', [AuthController::class, 'logout']);

Router::get('/mailbox', [MailboxController::class, 'inbox']);
Router::get('/mailbox/compose', [MailboxController::class, 'compose']);
Router::get('/mailbox/unread-count', [MailboxController::class, 'unreadCount']);
Router::post('/mailbox/send', [MailboxController::class, 'send']);
Router::post('/mailbox/read', [MailboxController::class, 'markAsRead']);

Router::dispatch();

<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct()
    {
        $this->initSession();
    }

    protected function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $timeoutSeconds = 20 * 60;
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSeconds)) {
                unset($_SESSION['user'], $_SESSION['last_activity']);
                session_regenerate_id(true);
                $this->setNotification('error', 'Byli jste automaticky odhlášeni z důvodu nečinnosti (20 minut).');
                $this->redirect('/login');
            }
            $_SESSION['last_activity'] = time();
        }
    }

    protected function setNotification(string $type, string $message): void
    {
        $_SESSION['notifications'][$type] = $message;
    }

    protected function getNotifications(): ?array
    {
        if (!isset($_SESSION['notifications'])) {
            return null;
        }
        $notifications = $_SESSION['notifications'];
        unset($_SESSION['notifications']);
        return $notifications;
    }

    protected function render(string $template, array $data = []): void
    {
        $data['isLogged'] = $this->isLogged();
        $data['isAdmin'] = $this->isAdmin();
        $data['user'] = $_SESSION['user'] ?? null;
        $data['notifications'] = $this->getNotifications();
        View::render($template, $data);
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function isLogged(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    protected function isAdmin(): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }
}

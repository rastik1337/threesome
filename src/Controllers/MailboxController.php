<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Encryption;
use App\Models\MessageRepo;
use App\Models\UserRepo;
use Throwable;

class MailboxController extends Controller
{
    public function inbox(): void
    {
        $this->requireAuth();

        $userId = intval($_SESSION["user"]["id"]);

        try {
            $incoming = MessageRepo::getIncomingAll($userId);
            $outgoing = MessageRepo::getOutgoingAll($userId);
        } catch (Throwable) {
            $this->setNotification("error", "Nepodařilo se načíst zprávy. Zkuste to prosím později.");
            $incoming = [];
            $outgoing = [];
        }

        $this->render("inbox", [
            "title" => "Poštovní schránka",
            "incoming" => $incoming,
            "outgoing" => $outgoing,
        ]);
    }

    public function compose(): void
    {
        $this->requireAuth();

        $this->render("compose", [
            "title" => "Nová zpráva",
        ]);
    }

    public function unreadCount(): void
    {
        $this->requireAuth();

        $userId = intval($_SESSION["user"]["id"]);
        $count = 0;

        try {
            $count = MessageRepo::getIncomingUnreadCount($userId);
        } catch (Throwable) {
        }

        header("Content-Type: application/json");
        echo json_encode(["count" => $count]);
        exit;
    }

    public function send(): void
    {
        $this->requireAuth();

        $userId = intval($_SESSION["user"]["id"]);
        $targetLogin = trim((string) ($_POST["target_login"] ?? ""));
        $subject = trim((string) ($_POST["subject"] ?? ""));
        $body = trim((string) ($_POST["body"] ?? ""));

        $errors = [];

        $target = UserRepo::getByLogin($targetLogin);
        if (!$target) {
            $errors[] = "Uživatel neexistuje.";
        } elseif ($target->user_id === $userId) {
            $errors[] = "Nemůžete poslat zprávu sami sobě.";
        }

        if (strlen($subject) > 255) {
            $errors[] = "Předmět zprávy může mít maximálně 255 znaků.";
        }

        if ($body === "") {
            $errors[] = "Vyplňte text zprávy.";
        } elseif (strlen($body) > 10000) {
            $errors[] = "Text zprávy je příliš dlouhý (maximum je 10 000 znaků).";
        }

        if (!empty($errors)) {
            $this->render("compose", [
                "title" => "Nová zpráva",
                "errors" => $errors,
                "old" => [
                    "target_login" => $targetLogin,
                    "subject" => $subject,
                    "body" => $body,
                ],
            ]);
            return;
        }

        try {
            MessageRepo::create([
                "source_id" => $userId,
                "target_id" => $target->user_id,
                "subject" => $subject !== "" ? Encryption::encrypt($subject) : null,
                "body" => Encryption::encrypt($body),
            ]);
        } catch (Throwable) {
            $this->render("compose", [
                "title" => "Nová zpráva",
                "errors" => ["Nastala chyba při odesílání zprávy. Zkuste to prosím znovu."],
                "old" => [
                    "target_login" => $targetLogin,
                    "subject" => $subject,
                    "body" => $body,
                ],
            ]);
            return;
        }

        $this->setNotification("success", "Zpráva byla úspěšně odeslána.");
        $this->redirect("/mailbox");
    }

    public function markAsRead(): void
    {
        $this->requireAuth();

        $userId = intval($_SESSION["user"]["id"]);
        $messageId = intval($_POST["message_id"] ?? 0);

        try {
            MessageRepo::setIsRead($messageId, $userId);
            $this->setNotification("success", "Zpráva byla označena jako přečtená.");
        } catch (Throwable) {
            $this->setNotification("error", "Nepodařilo se označit zprávu jako přečtenou.");
        }

        $this->redirect("/mailbox");
    }
}

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

    public function send(): void
    {
        $this->requireAuth();

        $userId = intval($_SESSION["user"]["id"]);
        $targetIdRaw = trim(($_POST["target_id"] ?? ""));
        $subject = trim(($_POST["subject"] ?? ""));
        $body = trim(($_POST["body"] ?? ""));

        $errors = [];

        if ($targetIdRaw === "") {
            $errors[] = "Vyplňte ID příjemce.";
        } elseif (!ctype_digit($targetIdRaw) || intval($targetIdRaw) <= 0) {
            $errors[] = "ID příjemce musí být kladné celé číslo.";
        } elseif (intval($targetIdRaw) === $userId) {
            $errors[] = "Nemůžete poslat zprávu sami sobě.";
        } elseif (!UserRepo::exists(intval($targetIdRaw))) {
            $errors[] = "Příjemce s tímto ID neexistuje.";
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
                    "target_id" => $targetIdRaw,
                    "subject" => $subject,
                    "body" => $body,
                ],
            ]);
            return;
        }

        try {
            MessageRepo::create([
                "source_id" => $userId,
                "target_id" => intval($targetIdRaw),
                "subject" => $subject !== "" ? Encryption::encrypt($subject) : null,
                "body" => Encryption::encrypt($body),
            ]);
        } catch (Throwable) {
            $this->render("compose", [
                "title" => "Nová zpráva",
                "errors" => ["Nastala chyba při odesílání zprávy. Zkuste to prosím znovu."],
                "old" => [
                    "target_id" => $targetIdRaw,
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
        $messageId = intval($_POST["message_id"]);

        try {
            MessageRepo::setIsRead($messageId, $userId);
            $this->setNotification("success", "Zpráva byla označena jako přečtená.");
        } catch (Throwable) {
            $this->setNotification("error", "Nepodařilo se označit zprávu jako přečtenou.");
        }

        $this->redirect("/mailbox");
    }
}

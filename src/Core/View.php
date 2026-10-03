<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data);

        $templateFile = __DIR__ . '/../Views/' . $template . '.php';

        if (!file_exists($templateFile)) {
            echo "Error: Template {$template} was not found.";
            return;
        }

        ob_start();
        require $templateFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../Views/layout.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}

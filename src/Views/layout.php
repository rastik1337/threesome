<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Aplikace') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        @layer base {
            button, input[type="button"], input[type="submit"], input[type="reset"] {
                all: revert;
                font-family: inherit;
                font-size: inherit;
            }
        }
    </style>
</head>

<body class="p-8">
    <nav class="flex gap-4 mb-6">
        <?php if ($isLogged ?? false): ?>
            <a href="/" class="text-blue-600 hover:text-blue-800 underline">Úvod</a>
        <?php else: ?>
            <a href="/login" class="text-blue-600 hover:text-blue-800 underline">Přihlášení</a>
            <a href="/register" class="text-blue-600 hover:text-blue-800 underline">Registrace</a>
        <?php endif; ?>
    </nav>

    <?php if (!empty($notifications['success'])): ?>
        <div class="border mb-4 p-2 text-sm font-medium">
            <?= htmlspecialchars($notifications['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($notifications['error'])): ?>
        <div class="border border-red-600 text-red-600 mb-4 p-2 text-sm font-medium">
            <?= htmlspecialchars($notifications['error']) ?>
        </div>
    <?php endif; ?>

    <main>
        <?= $content ?? '' ?>
    </main>
</body>

</html>

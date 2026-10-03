<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'App') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <nav class="flex gap-4 mb-6">
        <a href="/" class="text-blue-600 underline">Home</a>
        <a href="/page-two" class="text-blue-600 underline">Page Two</a>
        <a href="/page-three" class="text-blue-600 underline">Page Three</a>
    </nav>
    <main>
        <?= $content ?? '' ?>
    </main>
</body>

</html>

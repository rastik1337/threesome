<?php

/** @var array{id: int, login: string, role: string}|null $user */
?>
<h1 class="text-2xl font-bold mb-4">Úvodní stránka</h1>

<?php if ($user !== null): ?>
    <p>Vítejte v aplikaci, <strong><?= htmlspecialchars($user['login'] ?? '') ?></strong>!</p>
    <p class="text-sm mt-1">Vaše role v systému: <span class="font-medium"><?= htmlspecialchars($user['role'] ?? 'user') ?></span></p>
<?php else: ?>
    <p>Pro plný přístup k systému se prosím přihlaste nebo zaregistrujte.</p>
<?php endif; ?>

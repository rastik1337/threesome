<?php
/** @var array|null $user */
?>
<h1>Můj profil</h1>

<div>
    <p><strong>Uživatelské jméno:</strong> <?= htmlspecialchars($user['login'] ?? '') ?></p>
    <p><strong>Jméno:</strong> <?= htmlspecialchars($user['first_name'] ?? '') ?></p>
    <p><strong>Příjmení:</strong> <?= htmlspecialchars($user['last_name'] ?? '') ?></p>
    <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
    <p><strong>Telefon:</strong> <?= htmlspecialchars($user['phone'] ?? '') ?></p>
    <p><strong>Pohlaví:</strong> <?= htmlspecialchars($user['gender'] ?? '') ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? 'user') ?></p>
</div>

<?php

/**
 * @var array{id: int, login: string, role: string}|null $user
 */
?>
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
    <nav class="flex gap-4 mb-6 items-center">
        <?php if (!empty($user)): ?>
            <a href="/" class="text-blue-600 hover:text-blue-800 underline">Úvod</a>
            <a href="/profile" class="text-blue-600 hover:text-blue-800 underline">Profil</a>
            <a href="/mailbox" class="relative inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 underline">
                <span>Zprávy</span>
                <span id="navUnreadBadge" class="<?= ($unread_messages_count ?? 0) > 0 ? '' : 'hidden' ?> inline-flex items-center justify-center px-1.5 py-0.5 text-[11px] font-bold leading-none text-white bg-rose-600 rounded-full animate-pulse">
                    <?= intval($unread_messages_count ?? 0) ?>
                </span>
            </a>
            <form action="/logout" method="POST" class="inline">
                <button type="submit">
                    Odhlásit se (<?= htmlspecialchars($user['login'] ?? '') ?>)
                </button>
            </form>
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

    <?php if (!empty($user)): ?>
        <!-- Toast -->
        <div id="liveMessageToast" class="fixed bottom-5 right-5 z-50 hidden max-w-sm bg-white rounded-xl shadow-2xl border-2 border-blue-400 p-4 transition-all duration-300">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg shrink-0">
                    <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-slate-900">Nová příchozí zpráva!</h4>
                    <p class="text-xs text-slate-600 mt-0.5">Právě vám dorazila nová zpráva.</p>
                    <a href="/mailbox" class="inline-block mt-2 text-xs font-bold text-blue-600 hover:text-blue-800 underline">
                        Přejít do schránky &rarr;
                    </a>
                </div>
                <button type="button" onclick="document.getElementById('liveMessageToast').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <script>
            (function() {
                let previousUnread = <?= intval($unread_messages_count ?? 0) ?>;
                const badge = document.getElementById('navUnreadBadge');
                const toast = document.getElementById('liveMessageToast');

                function pollUnreadMessages() {
                    fetch('/mailbox/unread-count')
                        .then((res) => {
                            if (!res.ok) throw new Error();
                            return res.json();
                        })
                        .then((data) => {
                            const count = parseInt(data.count, 10) || 0;
                            if (badge) {
                                if (count > 0) {
                                    badge.textContent = count;
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            }
                            if (count > previousUnread) {
                                if (toast) {
                                    toast.classList.remove('hidden');
                                }
                            }
                            previousUnread = count;
                        })
                        .catch(() => {});
                }

                setInterval(pollUnreadMessages, 10000);
            })();
        </script>
    <?php endif; ?>
</body>

</html>

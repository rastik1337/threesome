<?php

declare(strict_types=1);

/**
 * @var array{id: int, login: string, role: string}|null $user
 * @var array<int, array<string, mixed>|object>|null $incoming
 * @var array<int, array<string, mixed>|object>|null $outgoing
 * @var array<string, string>|null $notifications
 */

$incomingMessages = $incoming ?? [];
$outgoingMessages = $outgoing ?? [];

$decryptText = static function (?string $text): string {
    if ($text === null || $text === '') {
        return '';
    }
    try {
        $decrypted = \App\Core\Encryption::decrypt($text);
        if ($decrypted !== false && $decrypted !== null && $decrypted !== '') {
            return (string) $decrypted;
        }
    } catch (\Throwable) {
        // Fallback to original text if decryption fails or text is plaintext
    }
    return $text;
};

$formatDate = static function (mixed $date): string {
    if ($date instanceof \DateTimeInterface) {
        return $date->format('d. m. Y H:i');
    }
    if (is_string($date) && $date !== '') {
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('d. m. Y H:i', $timestamp);
        }
        return $date;
    }
    return '-';
};

$normalizeMessage = static function (array|object $item) use ($decryptText, $formatDate): array {
    $isArr = is_array($item);
    $rawSubject = $isArr ? ($item['subject'] ?? null) : ($item->subject ?? null);
    $rawBody = $isArr ? ($item['body'] ?? '') : ($item->body ?? '');
    $isReadRaw = $isArr ? ($item['is_read'] ?? false) : ($item->is_read ?? false);
    $rawCreatedAt = $isArr ? ($item['created_at'] ?? null) : ($item->created_at ?? null);

    return [
        'id' => (int) ($isArr ? ($item['message_id'] ?? $item['id'] ?? 0) : ($item->message_id ?? $item->id ?? 0)),
        'source_id' => (int) ($isArr ? ($item['source_id'] ?? 0) : ($item->source_id ?? 0)),
        'target_id' => (int) ($isArr ? ($item['target_id'] ?? 0) : ($item->target_id ?? 0)),
        'subject' => $decryptText($rawSubject),
        'body' => $decryptText($rawBody),
        'is_read' => (bool) $isReadRaw,
        'created_at_formatted' => $formatDate($rawCreatedAt),
    ];
};

$unreadCount = 0;
$normalizedIncoming = [];
foreach ($incomingMessages as $msg) {
    $norm = $normalizeMessage($msg);
    if (!$norm['is_read']) {
        $unreadCount++;
    }
    $normalizedIncoming[] = $norm;
}

$normalizedOutgoing = [];
foreach ($outgoingMessages as $msg) {
    $normalizedOutgoing[] = $normalizeMessage($msg);
}
?>

<div class="max-w-4xl mx-auto my-4 sm:my-8 space-y-6">

    <!-- Upozornění na nové zprávy (Assignment: v případě nové příchozí zprávy se zobrazí uživateli notifikace) -->
    <div id="unreadBanner" class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 shadow-sm transition <?= $unreadCount === 0 ? 'hidden' : 'animate-pulse' ?>">
        <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <div id="unreadBannerText" class="text-sm font-semibold">
            Máte <span id="unreadCountSpan"><?= $unreadCount ?></span> <span id="unreadWordSpan"><?= $unreadCount === 1 ? 'novou nepřečtenou zprávu' : ($unreadCount < 5 ? 'nové nepřečtené zprávy' : 'nových nepřečtených zpráv') ?></span> v doručené poště.
        </div>
    </div>

    <!-- Hlavní karta -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">

        <!-- Záhlaví zpráv -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-md">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Poštovní schránka</h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-0.5">Interní komunikační systém pro výměnu zpráv s uživateli</p>
                    </div>
                </div>
            </div>

            <div>
                <a href="/mailbox/compose" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white shadow-md shadow-blue-900/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nová zpráva
                </a>
            </div>
        </div>

        <!-- Přepínání záložek (Příchozí / Odeslané) -->
        <div class="border-b border-slate-200 bg-slate-50/70 px-6 sm:px-8 flex gap-2 sm:gap-6">
            <button type="button" id="tabBtnIncoming" class="relative py-4 px-2 text-sm font-bold border-b-2 border-blue-600 text-blue-600 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span>Příchozí</span>
                <span id="tabIncomingCount" class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $unreadCount > 0 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' ?>">
                    <?= count($normalizedIncoming) ?><?= $unreadCount > 0 ? " ({$unreadCount} nové)" : "" ?>
                </span>
            </button>

            <button type="button" id="tabBtnOutgoing" class="relative py-4 px-2 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span>Odeslané</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-700">
                    <?= count($normalizedOutgoing) ?>
                </span>
            </button>
        </div>

        <!-- Obsah záložek -->
        <div class="p-6 sm:p-8">

            <!-- Záložka 1: Příchozí zprávy -->
            <div id="tabContentIncoming" class="space-y-4">
                <?php if (empty($normalizedIncoming)): ?>
                    <div class="text-center py-12 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Žádné příchozí zprávy</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Zatím vám nikdo nenapsal. Můžete někoho kontaktovat jako první.</p>
                        <a href="/mailbox/compose" class="inline-flex items-center gap-1.5 mt-4 text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            Napsat novou zprávu &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($normalizedIncoming as $item): ?>
                            <details class="incoming-message-item group bg-white rounded-xl border <?= !$item['is_read'] ? 'border-blue-300 ring-1 ring-blue-100 bg-blue-50/20 is-unread' : 'border-slate-200 is-read' ?> transition hover:border-slate-300 overflow-hidden shadow-sm"
                                     data-message-id="<?= htmlspecialchars((string) $item['id']) ?>"
                                     data-is-read="<?= $item['is_read'] ? '1' : '0' ?>">
                                <summary class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer list-none select-none">
                                    <div class="flex items-start sm:items-center gap-3">
                                        <div class="status-dot w-2.5 h-2.5 rounded-full shrink-0 mt-1.5 sm:mt-0 <?= !$item['is_read'] ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-slate-300' ?>"></div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                    Od: Uživatel #<?= htmlspecialchars((string) $item['source_id']) ?>
                                                </span>
                                                <span class="status-badge text-xs font-bold px-2 py-0.5 rounded-md <?= !$item['is_read'] ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600 font-medium' ?>">
                                                    <?= !$item['is_read'] ? 'Nová' : 'Přečteno' ?>
                                                </span>
                                            </div>
                                            <h4 class="subject-heading text-base <?= !$item['is_read'] ? 'font-bold' : 'font-semibold' ?> text-slate-900 mt-1 group-hover:text-blue-600 transition">
                                                <?= htmlspecialchars($item['subject'] !== '' ? $item['subject'] : 'Bez předmětu') ?>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 self-end sm:self-auto text-xs text-slate-400 font-medium">
                                        <time><?= htmlspecialchars($item['created_at_formatted']) ?></time>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </summary>
                                <div class="px-5 pb-5 pt-2 border-t border-slate-100 bg-slate-50/50">
                                    <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed font-normal bg-white p-4 rounded-lg border border-slate-200/80">
                                        <?= nl2br(htmlspecialchars($item['body'])) ?>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between gap-2 flex-wrap">
                                        <div class="read-action-container">
                                            <?php if (!$item['is_read']): ?>
                                                <form method="POST" action="/mailbox/read" class="mark-read-form inline">
                                                    <input type="hidden" name="message_id" value="<?= htmlspecialchars((string) $item['id']) ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm transition">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Označit jako přečtené
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Přečteno
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="/mailbox/compose?target_id=<?= urlencode((string) $item['source_id']) ?>&subject=<?= urlencode('Re: ' . $item['subject']) ?>" 
                                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                            </svg>
                                            Odpovědět
                                        </a>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Záložka 2: Odeslané zprávy -->
            <div id="tabContentOutgoing" class="space-y-4 hidden">
                <?php if (empty($normalizedOutgoing)): ?>
                    <div class="text-center py-12 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Žádné odeslané zprávy</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Složka odeslaných zpráv je prázdná. Všechny vámi odeslané zprávy se zobrazí zde.</p>
                        <a href="/mailbox/compose" class="inline-flex items-center gap-1.5 mt-4 text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            Napsat první zprávu &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($normalizedOutgoing as $item): ?>
                            <details class="group bg-white rounded-xl border border-slate-200 transition hover:border-slate-300 overflow-hidden shadow-sm">
                                <summary class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer list-none select-none">
                                    <div class="flex items-start sm:items-center gap-3">
                                        <div class="p-2 rounded-lg bg-slate-100 text-slate-500 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                    Komu: Uživatel #<?= htmlspecialchars((string) $item['target_id']) ?>
                                                </span>
                                                <?php if ($item['is_read']): ?>
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Přečteno příjemcem
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                                        Doručeno (zatím nepřečteno)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-900 mt-1 group-hover:text-blue-600 transition">
                                                <?= htmlspecialchars($item['subject'] !== '' ? $item['subject'] : 'Bez předmětu') ?>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 self-end sm:self-auto text-xs text-slate-400 font-medium">
                                        <time><?= htmlspecialchars($item['created_at_formatted']) ?></time>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </summary>
                                <div class="px-5 pb-5 pt-2 border-t border-slate-100 bg-slate-50/50">
                                    <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed font-normal bg-white p-4 rounded-lg border border-slate-200/80">
                                        <?= nl2br(htmlspecialchars($item['body'])) ?>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>

<script>
    (function() {
        const btnIncoming = document.getElementById('tabBtnIncoming');
        const btnOutgoing = document.getElementById('tabBtnOutgoing');
        const contentIncoming = document.getElementById('tabContentIncoming');
        const contentOutgoing = document.getElementById('tabContentOutgoing');
        const unreadBanner = document.getElementById('unreadBanner');
        const tabIncomingCount = document.getElementById('tabIncomingCount');
        const totalIncoming = <?= count($normalizedIncoming) ?>;
        let currentUnread = <?= (int) $unreadCount ?>;

        function showIncoming() {
            btnIncoming.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
            btnIncoming.classList.remove('border-transparent', 'text-slate-500', 'font-medium');

            btnOutgoing.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
            btnOutgoing.classList.add('border-transparent', 'text-slate-500', 'font-medium');

            contentIncoming.classList.remove('hidden');
            contentOutgoing.classList.add('hidden');
            window.location.hash = 'incoming';
        }

        function showOutgoing() {
            btnOutgoing.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
            btnOutgoing.classList.remove('border-transparent', 'text-slate-500', 'font-medium');

            btnIncoming.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
            btnIncoming.classList.add('border-transparent', 'text-slate-500', 'font-medium');

            contentOutgoing.classList.remove('hidden');
            contentIncoming.classList.add('hidden');
            window.location.hash = 'outgoing';
        }

        btnIncoming.addEventListener('click', showIncoming);
        btnOutgoing.addEventListener('click', showOutgoing);

        if (window.location.hash === '#outgoing') {
            showOutgoing();
        }

        function updateUnreadCount() {
            currentUnread = Math.max(0, currentUnread - 1);
            if (currentUnread === 0) {
                if (unreadBanner) {
                    unreadBanner.classList.add('hidden');
                }
                if (tabIncomingCount) {
                    tabIncomingCount.className = 'px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-700';
                    tabIncomingCount.textContent = totalIncoming;
                }
            } else {
                const countSpan = document.getElementById('unreadCountSpan');
                const wordSpan = document.getElementById('unreadWordSpan');
                if (countSpan) countSpan.textContent = currentUnread;
                if (wordSpan) {
                    wordSpan.textContent = currentUnread === 1 ? 'novou nepřečtenou zprávu' : (currentUnread < 5 ? 'nové nepřečtené zprávy' : 'nových nepřečtených zpráv');
                }
                if (tabIncomingCount) {
                    tabIncomingCount.textContent = totalIncoming + ' (' + currentUnread + ' nové)';
                }
            }
        }

        function markItemAsRead(item) {
            if (item.dataset.isRead === '1') return;
            item.dataset.isRead = '1';

            const messageId = item.dataset.messageId;

            // Visual update
            item.classList.remove('border-blue-300', 'ring-1', 'ring-blue-100', 'bg-blue-50/20', 'is-unread');
            item.classList.add('border-slate-200', 'is-read');

            const dot = item.querySelector('.status-dot');
            if (dot) {
                dot.className = 'status-dot w-2.5 h-2.5 rounded-full shrink-0 mt-1.5 sm:mt-0 bg-slate-300';
            }

            const badge = item.querySelector('.status-badge');
            if (badge) {
                badge.className = 'status-badge text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-600';
                badge.textContent = 'Přečteno';
            }

            const heading = item.querySelector('.subject-heading');
            if (heading) {
                heading.classList.remove('font-bold');
                heading.classList.add('font-semibold');
            }

            const actionContainer = item.querySelector('.read-action-container');
            if (actionContainer) {
                actionContainer.innerHTML = '<span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium"><svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Přečteno</span>';
            }

            updateUnreadCount();

            // Send backend request
            fetch('/mailbox/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'message_id=' + encodeURIComponent(messageId)
            }).catch(console.error);
        }

        // Auto-mark as read when user expands the message details
        document.querySelectorAll('.incoming-message-item').forEach((item) => {
            item.addEventListener('toggle', () => {
                if (item.open && item.dataset.isRead === '0') {
                    markItemAsRead(item);
                }
            });

            // Handle manual "Označit jako přečtené" button click
            const form = item.querySelector('.mark-read-form');
            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    markItemAsRead(item);
                });
            }
        });
    })();
</script>

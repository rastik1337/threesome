<?php

declare(strict_types=1);

/**
 * @var array{id: int, login: string, role: string}|null $user
 * @var array<string, string>|null $notifications
 * @var list<string>|null $errors
 * @var array{target_login?: string, subject?: string, body?: string}|null $old
 */

$prefillTargetLogin = htmlspecialchars((string) ($old['target_login'] ?? $_GET['target_login'] ?? $_GET['to'] ?? ''));
$prefillSubject = htmlspecialchars((string) ($old['subject'] ?? $_GET['subject'] ?? ''));
$prefillBody = htmlspecialchars((string) ($old['body'] ?? ''));
$currentLogin = (string) ($user['login'] ?? '');
?>

<div class="max-w-2xl mx-auto my-4 sm:my-8">

    <!-- Odkaz zpět -->
    <div class="mb-4">
        <a href="/mailbox" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Zpět do schránky
        </a>
    </div>

    <!-- Karta formuláře -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">

        <!-- Záhlaví formuláře -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Nová zpráva</h1>
                    <p class="text-xs sm:text-sm text-blue-100 mt-0.5">Odešlete interní zprávu jinému uživateli</p>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="m-6 sm:m-8 mb-0 p-4 rounded-xl bg-rose-50 border border-rose-200">
                <div class="text-sm font-semibold text-rose-800 mb-1">Chyba při odesílání zprávy:</div>
                <ul class="text-rose-700 text-sm list-disc list-inside space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulář -->
        <form id="sendMessageForm" method="POST" action="/mailbox/send" novalidate class="p-6 sm:p-8 space-y-5">

            <!-- Příjemce -->
            <div>
                <label for="target_login" class="block text-sm font-bold text-slate-700 mb-1">
                    Příjemce (uživatelské jméno / login): <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="target_login" 
                           name="target_login" 
                           required 
                           value="<?= $prefillTargetLogin ?>"
                           placeholder="např. novak"
                           autocomplete="off"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                </div>
                <p class="text-xs text-slate-500 mt-1">Zadejte přihlašovací jméno (login) uživatele, kterému chcete zprávu doručit.</p>
                <div id="targetLoginError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <!-- Předmět (subject) -->
            <div>
                <label for="subject" class="block text-sm font-bold text-slate-700 mb-1">
                    Předmět zprávy <span class="text-xs font-normal text-slate-400">(volitelné)</span>:
                </label>
                <input type="text" 
                       id="subject" 
                       name="subject" 
                       value="<?= $prefillSubject ?>"
                       placeholder="např. Schůzka k projektu / Dotaz"
                       maxlength="255"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="subjectError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <!-- Text zprávy (body) -->
            <div>
                <label for="body" class="block text-sm font-bold text-slate-700 mb-1">
                    Text zprávy: <span class="text-rose-500">*</span>
                </label>
                <textarea id="body" 
                          name="body" 
                          rows="7" 
                          required 
                          placeholder="Sem napište obsah vaší zprávy..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors resize-y"><?= $prefillBody ?></textarea>
                <div id="bodyError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <!-- Tlačítka akcí -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="/mailbox" class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Zrušit
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Odeslat zprávu
                </button>
            </div>

        </form>

    </div>
</div>

<script>
    (function() {
        const form = document.getElementById('sendMessageForm');
        const currentLogin = <?= json_encode($currentLogin) ?>.toLowerCase();

        const fields = {
            target_login: {
                input: document.getElementById('target_login'),
                error: document.getElementById('targetLoginError'),
                validate: (val) => {
                    const trimmed = val.trim();
                    if (!trimmed) {
                        return 'Zadejte přihlašovací jméno příjemce.';
                    }
                    if (currentLogin && trimmed.toLowerCase() === currentLogin) {
                        return 'Nemůžete poslat zprávu sami sobě.';
                    }
                    return '';
                }
            },
            subject: {
                input: document.getElementById('subject'),
                error: document.getElementById('subjectError'),
                validate: (val) => {
                    if (val.trim().length > 255) {
                        return 'Předmět zprávy může mít maximálně 255 znaků.';
                    }
                    return '';
                }
            },
            body: {
                input: document.getElementById('body'),
                error: document.getElementById('bodyError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Napište text zprávy.';
                    }
                    return '';
                }
            }
        };

        function validateSingle(key) {
            const item = fields[key];
            const val = item.input.value;
            const msg = item.validate(val);
            item.error.textContent = msg;

            if (msg) {
                item.input.classList.add('!border-rose-500', 'focus:!ring-rose-400');
                item.input.classList.remove('border-slate-300', 'focus:ring-blue-500');
                return false;
            } else {
                item.input.classList.remove('!border-rose-500', 'focus:!ring-rose-400');
                item.input.classList.add('border-slate-300', 'focus:ring-blue-500');
                return true;
            }
        }

        Object.keys(fields).forEach((key) => {
            const input = fields[key].input;
            input.addEventListener('blur', () => {
                validateSingle(key);
            });
            input.addEventListener('input', () => {
                validateSingle(key);
            });
        });

        form.addEventListener('submit', (e) => {
            let formValid = true;
            let firstInvalid = null;

            Object.keys(fields).forEach((key) => {
                const isOk = validateSingle(key);
                if (!isOk) {
                    formValid = false;
                    firstInvalid ??= fields[key].input;
                }
            });

            if (!formValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    })();
</script>

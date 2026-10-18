<div class="max-w-2xl mx-auto my-4 sm:my-8 bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
    
    <!-- Záhlaví profilu s gradientem -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 h-28 sm:h-36 relative"></div>

    <!-- Hlavní karta profilu -->
    <div class="px-6 sm:px-8 pb-8 -mt-12 sm:-mt-16 relative">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
            <!-- Profilová fotka / Avatar -->
            <div class="flex items-end gap-4">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white p-1.5 shadow-md border border-slate-200/80 overflow-hidden shrink-0">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="/uploads/<?= htmlspecialchars($user['avatar']) ?>" 
                             alt="Avatar" 
                             class="w-full h-full object-cover rounded-xl">
                    <?php else: ?>
                        <div class="w-full h-full bg-blue-50 text-blue-600 font-bold text-3xl flex items-center justify-center rounded-xl uppercase">
                            <?= htmlspecialchars(mb_substr($user['login'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['login'] ?? 'Uživatel')) ?>
                    </h1>
                    <p class="text-sm text-slate-500 font-medium">@<?= htmlspecialchars($user['login'] ?? '') ?></p>
                </div>
            </div>

            <!-- Role uživatele -->
            <div class="self-start sm:self-end">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase bg-blue-50 text-blue-700 border border-blue-200">
                    Role: <?= htmlspecialchars($user['role'] ?? 'user') ?>
                </span>
            </div>
        </div>

        <!-- Mřížka s osobními údajy -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Uživatelské jméno</span>
                <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($user['login'] ?? '-') ?></span>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">E-mailová adresa</span>
                <span class="text-sm font-bold text-slate-800 break-all"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Jméno a Příjmení</span>
                <span class="text-sm font-bold text-slate-800">
                    <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: '-') ?>
                </span>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Telefonní číslo</span>
                <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($user['phone'] ?? '-') ?></span>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Pohlaví</span>
                <span class="text-sm font-bold text-slate-800">
                    <?php 
                        $genders = ['male' => 'Muž', 'female' => 'Žena', 'other' => 'Jiné / Neuvedeno'];
                        echo htmlspecialchars($genders[$user['gender'] ?? ''] ?? ($user['gender'] ?? '-'));
                    ?>
                </span>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Typ účtu</span>
                <span class="text-sm font-bold text-slate-800 capitalize"><?= htmlspecialchars($user['role'] ?? 'Uživatel') ?></span>
            </div>

        </div>

        <!-- Tlačítka dole -->
        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <a href="/" class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                &larr; Zpět na hlavní stránku
            </a>
            <a href="/logout" class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                Odhlásit se z účtu
            </a>
        </div>

    </div>  
</div>
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg border border-slate-100 p-6 sm:p-8 my-6">
    <div class="mb-6 border-b border-slate-100 pb-4 text-center">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Přihlášení</h1>
        <p class="text-sm text-slate-500 mt-1">Zadejte své přihlašovací údaje pro vstup do účtu.</p>
    </div>

    <!-- Serverové chyby z PHP -->
    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200">
            <div class="text-sm font-semibold text-rose-800 mb-1">Chyba při přihlášení:</div>
            <ul class="text-rose-700 text-sm list-disc list-inside space-y-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulář -->
    <form id="loginForm" method="POST" action="/login" novalidate class="space-y-4">
        <div>
            <label for="login" class="block text-sm font-semibold text-slate-700 mb-1">Uživatelské jméno (login):</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" 
                   placeholder="uzivatel123" required
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
            <div id="loginError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Heslo:</label>
            <input type="password" id="password" name="password" 
                   placeholder="••••••••" required
                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
            <div id="passwordError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 rounded-lg shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition cursor-pointer">
                Přihlásit se
            </button>
        </div>

        <p class="text-center text-sm text-slate-600 pt-3 border-t border-slate-100">
            Nemáte ještě účet? <a href="/register" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Vytvořte ho zde</a>.
        </p>
    </form>
</div>

<script>
    (function() {
        const form = document.getElementById('loginForm');
        const fields = {
            login: {
                input: document.getElementById('login'),
                error: document.getElementById('loginError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Vyplňte přihlašovací jméno.';
                    } else {
                        return '';
                    }
                }
            },
            password: {
                input: document.getElementById('password'),
                error: document.getElementById('passwordError'),
                validate: (val) => {
                    if (!val) {
                        return 'Vyplňte heslo.';
                    } else {
                        return '';
                    }
                }
            }
        };

        Object.keys(fields).forEach((key) => {
            const input = fields[key].input;
            if (!input) return;
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

            Object.keys(fields).forEach(function(key) {
                const isOk = validateSingle(key);
                if (!isOk) {
                    formValid = false;
                    if (!firstInvalid) {
                        firstInvalid = fields[key].input;
                    }
                }
            });

            if (!formValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

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
    })();
</script>
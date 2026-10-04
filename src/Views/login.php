<h1 class="text-2xl font-bold mb-4">Přihlášení</h1>

<?php if (!empty($errors)): ?>
    <ul class="text-red-600 text-sm mb-4 list-disc list-inside">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form id="loginForm" method="POST" action="/login" novalidate>
    <div class="mb-3">
        <label for="login" class="block font-medium mb-1">Uživatelské jméno (login):</label>
        <input type="text" id="login" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" class="border" required>
        <div id="loginError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-4">
        <label for="password" class="block font-medium mb-1">Heslo:</label>
        <input type="password" id="password" name="password" class="border" required>
        <div id="passwordError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div>
        <button type="submit">
            Přihlásit se
        </button>
    </div>

    <p class="text-sm">
        Nemáte ještě účet? <a href="/register" class="text-blue-600 underline">Vytvořte ho zde</a>.
    </p>
</form>

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
                item.input.classList.add('border-red-600');
                return false;
            } else {
                item.input.classList.remove('border-red-600');
                return true;
            }
        }
    })();
</script>

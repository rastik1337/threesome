<h1 class="text-2xl font-bold mb-4">Registrace uživatele</h1>

<?php if (!empty($errors)): ?>
    <ul class="text-red-600 text-sm mb-4 list-disc list-inside">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form id="registerForm" method="POST" action="/register" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
        <label for="first_name" class="block font-medium mb-1">Jméno:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" class="border" required>
        <div id="firstNameError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="last_name" class="block font-medium mb-1">Příjmení:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" class="border" required>
        <div id="lastNameError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="email" class="block font-medium mb-1">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="border" required>
        <div id="emailError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="phone" class="block font-medium mb-1">Telefonní číslo:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="+420 777 546 332" class="border" required>
        <div id="phoneError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="gender" class="block font-medium mb-1">Pohlaví:</label>
        <select id="gender" name="gender" class="border" required>
            <option value="">-- Vyberte pohlaví --</option>
            <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Muž</option>
            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Žena</option>
            <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Jiné / Neuvedeno</option>
        </select>
        <div id="genderError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="avatar" class="block font-medium mb-1">Profilová fotografie (PNG, GIF, BMP, TIFF, JPEG):</label>
        <input type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.gif,.bmp,.tif,.tiff" class="text-sm" required>
        <div id="avatarError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="login" class="block font-medium mb-1">Uživatelské jméno (login):</label>
        <input type="text" id="login" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" class="border" required minlength="3">
        <div id="loginError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div class="mb-4">
        <label for="password" class="block font-medium mb-1">Heslo:</label>
        <input type="password" id="password" name="password" class="border" required minlength="6">
        <div id="passwordError" class="text-red-600 text-sm mt-1"></div>
    </div>

    <div>
        <button type="submit">
            Zaregistrovat se
        </button>
    </div>

    <p class="text-sm">
        Již máte účet? <a href="/login" class="text-blue-600 underline">Přihlaste se zde</a>.
    </p>
</form>

<script>
    (function() {
        const fields = {
            first_name: {
                input: document.getElementById('first_name'),
                error: document.getElementById('firstNameError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Vyplňte jméno.';
                    }
                    if (val.trim().length < 2) {
                        return 'Jméno musí mít alespoň 2 znaky.';
                    }
                    return '';
                }
            },
            last_name: {
                input: document.getElementById('last_name'),
                error: document.getElementById('lastNameError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Vyplňte příjmení.';
                    }
                    if (val.trim().length < 2) {
                        return 'Příjmení musí mít alespoň 2 znaky.';
                    }
                    return '';
                }
            },
            email: {
                input: document.getElementById('email'),
                error: document.getElementById('emailError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Vyplňte e-mail.';
                    }
                    const regex = /^[a-zA-Z0-9._-]+@[^\s@]+\.[^\s@]+$/;
                    if (!regex.test(val.trim())) {
                        return 'Zadejte platný formát e-mailu (např. jmeno@domena.cz).';
                    }
                    return '';
                }
            },
            phone: {
                input: document.getElementById('phone'),
                error: document.getElementById('phoneError'),
                validate: (val) => {
                    const trimmed = val.trim();
                    if (!trimmed) {
                        return 'Vyplňte telefonní číslo.';
                    }
                    const regex = /^(\+420|\+421)?\s?([1-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3})$/;
                    if (!regex.test(trimmed)) {
                        return 'Zadejte platné české nebo slovenské telefonní číslo (např. +420 123 456 789).';
                    }
                    return '';
                }
            },
            gender: {
                input: document.getElementById('gender'),
                error: document.getElementById('genderError'),
                validate: (val) => {
                    if (!val) {
                        return 'Vyberte pohlaví ze seznamu.';
                    }
                    return '';
                }
            },
            avatar: {
                input: document.getElementById('avatar'),
                error: document.getElementById('avatarError'),
                validate: () => {
                    const files = fields.avatar.input.files;
                    if (!files || files.length === 0) {
                        return 'Vyberte profilovou fotografii.';
                    }
                    const allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff'];
                    const ext = files[0].name.split('.').pop().toLowerCase();
                    if (!allowed.includes(ext)) {
                        return 'Nepodporovaný formát. Povolené jsou: PNG, GIF, BMP, TIFF, JPEG.';
                    }
                    return '';
                }
            },
            login: {
                input: document.getElementById('login'),
                error: document.getElementById('loginError'),
                validate: (val) => {
                    if (!val.trim()) {
                        return 'Vyplňte uživatelské jméno.';
                    }
                    if (val.trim().length < 3) {
                        return 'Uživatelské jméno musí mít alespoň 3 znaky.';
                    }
                    const regex = /^[a-zA-Z0-9_-]+$/;
                    if (!regex.test(val.trim())) {
                        return 'Povoleny jsou pouze písmena, číslice, podtržítko a pomlčka.';
                    }
                    return '';
                }
            },
            password: {
                input: document.getElementById('password'),
                error: document.getElementById('passwordError'),
                validate: (val) => {
                    if (!val) {
                        return 'Vyplňte heslo.';
                    }
                    if (val.length < 8) {
                        return 'Heslo musí mít alespoň 8 znaků.';
                    }
                    return '';
                }
            }
        };
        const form = document.getElementById('registerForm');

        Object.keys(fields).forEach((key) => {
            const input = fields[key].input;
            input.addEventListener('blur', function() {
                validateSingle(key);
            });
            input.addEventListener('input', function() {
                validateSingle(key);
            });
            input.addEventListener('change', function() {
                validateSingle(key);
            });
        });

        form.addEventListener('submit', function(e) {
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

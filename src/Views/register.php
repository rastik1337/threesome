<div class="max-w-xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-100 p-6 sm:p-8 my-6">
    <div class="mb-6 border-b border-slate-100 pb-4">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Registrace uživatele</h1>
        <p class="text-sm text-slate-500 mt-1">Vyplňte prosím níže uvedené údaje pro vytvoření nového profilu.</p>
    </div>

    <!-- Serverové chybové hlášky z PHP -->
    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200">
            <div class="text-sm font-semibold text-rose-800 mb-1">Při registraci došlo k chybám:</div>
            <ul class="text-rose-700 text-sm list-disc list-inside space-y-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Registrační formulář -->
    <form id="registerForm" method="POST" action="/register" enctype="multipart/form-data" novalidate class="space-y-4">
        
        <!-- Jméno a Příjmení vedle sebe na větších displejích -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-semibold text-slate-700 mb-1">Jméno:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" 
                       placeholder="Jan" required
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="firstNameError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <div>
                <label for="last_name" class="block text-sm font-semibold text-slate-700 mb-1">Příjmení:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" 
                       placeholder="Novák" required
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="lastNameError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>
        </div>

        <!-- E-mail a Telefonní číslo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">E-mail:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                       placeholder="jan.novak@priklad.cz" required
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="emailError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Telefonní číslo:</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                       placeholder="+420 777 546 332" required
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="phoneError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>
        </div>

        <!-- Pohlaví -->
        <div>
            <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1">Pohlaví:</label>
            <select id="gender" name="gender" required
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <option value="">-- Vyberte pohlaví --</option>
                <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Muž</option>
                <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Žena</option>
                <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Jiné / Neuvedeno</option>
            </select>
            <div id="genderError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
        </div>

        <!-- Profilová fotografie -->
        <div>
            <label for="avatar" class="block text-sm font-semibold text-slate-700 mb-1">Profilová fotografie (PNG, GIF, BMP, TIFF, JPEG):</label>
            <input type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.gif,.bmp,.tif,.tiff" required
                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-300 rounded-lg p-1.5 bg-slate-50/50">
            <div id="avatarError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
        </div>

        <!-- Uživatelské jméno a Heslo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="login" class="block text-sm font-semibold text-slate-700 mb-1">Uživatelské jméno (login):</label>
                <input type="text" id="login" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" 
                       placeholder="uzivatel123" required minlength="3"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="loginError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Heslo:</label>
                <input type="password" id="password" name="password" 
                       placeholder="••••••••" required minlength="6"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-900 transition-colors">
                <div id="passwordError" class="text-rose-600 text-xs mt-1 min-h-[1rem]"></div>
            </div>
        </div>

        <!-- Odesílací tlačítko -->
        <div class="pt-3">
            <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 rounded-lg shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition cursor-pointer">
                Zaregistrovat se
            </button>
        </div>

        <p class="text-center text-sm text-slate-600 pt-3">
            Již máte účet? <a href="/login" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Přihlaste se zde</a>.
        </p>
    </form>
</div>

<script>
    (function() {
        const fields = {
            first_name: {
                input: document.getElementById('first_name'),
                error: document.getElementById('firstNameError'),
                validate: (val) => {
                    if (!val.trim()) return 'Vyplňte jméno.';
                    if (val.trim().length < 2) return 'Jméno musí mít alespoň 2 znaky.';
                    return '';
                }
            },
            last_name: {
                input: document.getElementById('last_name'),
                error: document.getElementById('lastNameError'),
                validate: (val) => {
                    if (!val.trim()) return 'Vyplňte příjmení.';
                    if (val.trim().length < 2) return 'Příjmení musí mít alespoň 2 znaky.';
                    return '';
                }
            },
            email: {
                input: document.getElementById('email'),
                error: document.getElementById('emailError'),
                validate: (val) => {
                    if (!val.trim()) return 'Vyplňte e-mail.';
                    const regex = /^[a-zA-Z0-9._-]+@[^\s@]+\.[^\s@]+$/;
                    if (!regex.test(val.trim())) return 'Zadejte platný formát e-mailu (např. jmeno@domena.cz).';
                    return '';
                }
            },
            phone: {
                input: document.getElementById('phone'),
                error: document.getElementById('phoneError'),
                validate: (val) => {
                    const trimmed = val.trim();
                    if (!trimmed) return 'Vyplňte telefonní číslo.';
                    const regex = /^(\+420|\+421)?\s?([1-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3})$/;
                    if (!regex.test(trimmed)) return 'Zadejte platné telefonní číslo (např. +420 123 456 789).';
                    return '';
                }
            },
            gender: {
                input: document.getElementById('gender'),
                error: document.getElementById('genderError'),
                validate: (val) => {
                    if (!val) return 'Vyberte pohlaví ze seznamu.';
                    return '';
                }
            },
            avatar: {
                input: document.getElementById('avatar'),
                error: document.getElementById('avatarError'),
                validate: () => {
                    const files = fields.avatar.input.files;
                    if (!files || files.length === 0) return 'Vyberte profilovou fotografii.';
                    const allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff'];
                    const ext = files[0].name.split('.').pop().toLowerCase();
                    if (!allowed.includes(ext)) return 'Nepodporovaný formát. Povolené: PNG, GIF, BMP, TIFF, JPEG.';
                    return '';
                }
            },
            login: {
                input: document.getElementById('login'),
                error: document.getElementById('loginError'),
                validate: (val) => {
                    if (!val.trim()) return 'Vyplňte uživatelské jméno.';
                    if (val.trim().length < 3) return 'Uživatelské jméno musí mít alespoň 3 znaky.';
                    const regex = /^[a-zA-Z0-9_-]+$/;
                    if (!regex.test(val.trim())) return 'Povoleny jsou pouze písmena, číslice, podtržítko a pomlčka.';
                    return '';
                }
            },
            password: {
                input: document.getElementById('password'),
                error: document.getElementById('passwordError'),
                validate: (val) => {
                    if (!val) return 'Vyplňte heslo.';
                    if (val.length < 8) return 'Heslo musí mít alespoň 8 znaků.';
                    return '';
                }
            }
        };

        const form = document.getElementById('registerForm');

        Object.keys(fields).forEach((key) => {
            const input = fields[key].input;
            if (!input) return;
            ['blur', 'input', 'change'].forEach(evt => {
                input.addEventListener(evt, () => validateSingle(key));
            });
        });

        form.addEventListener('submit', function(e) {
            let formValid = true;
            let firstInvalid = null;

            Object.keys(fields).forEach(function(key) {
                const isOk = validateSingle(key);
                if (!isOk) {
                    formValid = false;
                    if (!firstInvalid) firstInvalid = fields[key].input;
                }
            });

            if (!formValid) {
                e.preventDefault();
                if (firstInvalid) firstInvalid.focus();
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
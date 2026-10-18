-- (Password is 'admin' hashed with bcrypt. Replace [ENCRYPTED_...] with actual encrypted strings from your PHP backend)
INSERT INTO users (login, password, first_name, last_name, email, phone, role)
VALUES (
    'admin',
    'password',
    'first_name',
    'last_name',
    'admin@gallery.com',
    "777111555",
    'admin'
);

-- (Password is 'admin' hashed with bcrypt. Replace [ENCRYPTED_...] with actual encrypted strings from your PHP backend)
INSERT INTO users (username, password, first_name, last_name, email, role)
VALUES (
    'admin',
    'password',
    'first_name',
    'last_name',
    'admin@gallery.com',
    'admin'
);

--- (Password is 'admin' hashed with bcrypt.)
INSERT INTO users (login, password, first_name, last_name, email, phone, gender, avatar_path, role)
VALUES (
    'admin',
    '$2y$12$YpaW4gV0Ih3MbsFnAKXtAee5VbBYsTadgfrHqIk4ygeIuaqWx1Pry',
    '3FPAOwiMqDhBDXTjdLd4VTF2YXQvblNUSXZwclhyY2dUY2RENWc9PQ==',
    '7PpC6beEUEtDeh6xasJsoWFwRUFWaWRwNzJVRkpZenFjUkl5YUE9PQ==',
    'QHJq4S/EQ3+vEQnnTSfDL1lPUXZ1OVVSM3d6aDVmNjlweFF0bjAwbzZjVHpUbjEwUVVyNjRKSEdFY0U9',
    'yImBEXwKShlTokxSc87QzTQzdjM4RVZGZ1l0RVpDTXpPZ1IxMVE9PQ==',
    'other',
    NULL,
    'admin'
);

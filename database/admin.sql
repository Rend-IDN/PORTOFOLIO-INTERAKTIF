-- Hapus user admin yang bermasalah
DELETE FROM users WHERE username = 'admin';

-- Buat user admin baru
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
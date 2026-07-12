-- =============================================
-- DATABASE PORTOFOLIO INTERAKTIF
-- =============================================

CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin (password: admin123)
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- Tabel portfolio_items
CREATE TABLE IF NOT EXISTS portfolio_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    image_url VARCHAR(255),
    project_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Data contoh portfolio
INSERT INTO portfolio_items (title, description, category, image_url, project_url) VALUES
('E-Commerce Website Modern', 'Platform belanja online lengkap dengan fitur keranjang dan payment gateway.', 'Web Development', '', 'https://example.com'),
('Dashboard Analytics Pro', 'Sistem monitoring data real-time dengan chart interaktif.', 'Dashboard', '', 'https://example.com'),
('Mobile App UI/UX Design', 'Desain aplikasi mobile modern dengan Figma.', 'UI/UX Design', '', 'https://example.com'),
('Company Profile Perusahaan', 'Website profil perusahaan profesional dan responsif.', 'Web Development', '', 'https://example.com'),
('Aplikasi Fitness Tracker', 'Tracking olahraga dan kesehatan berbasis mobile.', 'Mobile App', '', 'https://example.com');

-- Tabel messages
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Portofolio Interaktif'),
('contact_email', 'admin@example.com'),
('contact_phone', '+62 812 3456 7890')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

SELECT 'Database berhasil dibuat!' AS status;
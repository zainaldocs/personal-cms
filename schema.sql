CREATE DATABASE IF NOT EXISTS personal_cms;
USE personal_cms;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: posts (Blog)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: portfolios
CREATE TABLE IF NOT EXISTS portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NULL,
    project_url VARCHAR(255) NULL,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: messages (Contact Inbox)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin user (username: admin, password: admin123)
INSERT INTO users (username, password_hash, email) 
VALUES ('admin', '$2y$10$TpPLYer4j/0dF/eNTiHARutZqfYa/pCmuBg6.ZNWUs4/B3Ih.g.ba', 'admin@example.com')
ON DUPLICATE KEY UPDATE username=username;

-- Seed default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'My Personal CMS'),
('hero_title', 'Hi, I am Zainal Arifin'),
('hero_subtitle', 'Fullstack Developer & Content Creator'),
('about_title', 'About Me'),
('about_text', 'I am a passionate software developer with experience in building web applications using PHP, MySQL, JavaScript, and Tailwind CSS. Welcome to my personal website!'),
('about_image', ''),
('contact_email', 'zainal@example.com'),
('contact_phone', '+628123456789'),
('contact_address', 'Jakarta, Indonesia'),
('social_github', 'https://github.com/zainaldocs'),
('social_linkedin', 'https://linkedin.com/in/zainal'),
('social_twitter', 'https://twitter.com/zainal')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

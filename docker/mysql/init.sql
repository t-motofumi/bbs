CREATE DATABASE IF NOT EXISTS bbs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE bbs;

-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 投稿テーブル
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 初期管理者アカウントの追加（パスワードは"adminpass" のハッシュ）
INSERT INTO users (username, password_hash, role)
VALUES (
    'admin',
    '$2y$10$Wz72v4gKpVtpOq2lyqBGQOcd19zOILaOJViPoA1TbkQFJkwNdCQja', -- 'adminpass'
    'admin'
);

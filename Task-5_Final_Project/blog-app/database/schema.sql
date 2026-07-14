CREATE DATABASE IF NOT EXISTS blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog;

DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'editor', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_posts_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'admin'),
('Editor', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'editor'),
('User', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'user');

INSERT INTO posts (user_id, title, content) VALUES
(1, 'Welcome to the Internship Blog', 'This post confirms the CRUD application is connected to MySQL and ready for testing.'),
(2, 'Search and Pagination Added', 'The listing page supports keyword search and paginated results for a better user experience.'),
(1, 'Security Enhancements Complete', 'Prepared statements, validation, CSRF tokens, password hashing, and role checks are implemented.');

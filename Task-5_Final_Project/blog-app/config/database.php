<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'blog';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        $dataDir = __DIR__ . '/../data';
        $dbPath = $dataDir . '/blog.sqlite';

        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }

        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        initialize_sqlite_database($pdo);
    }

    return $pdo;
}

function initialize_sqlite_database(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");

    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

    if ($count > 0) {
        return;
    }

    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['Admin', 'admin@example.com', $hash, 'admin']);
    $stmt->execute(['Editor', 'editor@example.com', $hash, 'editor']);
    $stmt->execute(['User', 'user@example.com', $hash, 'user']);

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
    $stmt->execute([1, 'Welcome to the Internship Blog', 'This post confirms the CRUD application is connected and ready for testing.']);
    $stmt->execute([2, 'Search and Pagination Added', 'The listing page supports keyword search and paginated results for a better user experience.']);
    $stmt->execute([1, 'Security Enhancements Complete', 'Prepared statements, validation, CSRF tokens, password hashing, and role checks are implemented.']);
}

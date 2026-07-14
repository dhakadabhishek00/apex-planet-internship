# Blog App - PHP & MySQL Internship Final Project

This is a complete PHP/MySQL blog application for the 45-day internship task pack.

## Features

- User registration and login.
- Password hashing.
- CRUD posts.
- Search posts by title or content.
- Paginated post listing.
- PDO prepared statements.
- CSRF protection for create, update, and delete actions.
- Role-based access control.
- Responsive UI.

## Requirements

- PHP 8.0 or newer.
- MySQL or MariaDB.
- Apache through XAMPP, WAMP, MAMP, or similar.

## Setup

1. Create a database by importing `database/schema.sql`.
2. Edit `config/database.php` if your MySQL username or password is different.
3. Put this folder in your web root.
4. Visit `/blog-app/public`.

## Demo Users

- Admin: `admin@example.com` / `password`
- Editor: `editor@example.com` / `password`
- User: `user@example.com` / `password`

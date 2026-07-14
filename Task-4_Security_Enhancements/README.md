# Task 4 - Security Enhancements

## Completed Deliverables

- PDO prepared statements for all database queries.
- Server-side form validation.
- Basic client-side required fields.
- Password hashing with `password_hash`.
- Session-based authentication.
- CSRF tokens for write actions.
- Role-based access control with `admin`, `editor`, and `user` roles.

## Security Notes

- Admins can edit and delete all posts.
- Editors can create posts and manage their own posts.
- Users can view posts.
- All database writes use prepared statements.


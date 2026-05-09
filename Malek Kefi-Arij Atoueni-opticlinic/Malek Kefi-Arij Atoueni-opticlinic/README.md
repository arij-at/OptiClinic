# OptiClinic Deployment Guide

## Deployment Steps

1. Upload the project files to your web server document root.
2. Create the database and import `database.sql`:
   ```sql
   mysql -u <db_user> -p < database.sql
   ```
3. Copy `.env.example` to `.env` and set your database credentials:
   ```env
   DB_HOST=your-db-host
   DB_PORT=3306
   DB_NAME=opticlinic
   DB_USER=your-db-user
   DB_PASS=your-db-password
   ```
4. Ensure the web server user can read the project files and write sessions if required.
5. Visit the site in a browser and log in with a demo account.

## Demo Credentials

The database schema includes sample staff accounts. You can also insert your own with a PHP-hashed password.

Receptionist:
- Email: `rec@clinic.com`
- Password: `password`

Doctor:
- Email: `doc@clinic.com`
- Password: `password`

Example SQL using `password_hash()`:
```php
$hash = password_hash('password', PASSWORD_DEFAULT);
echo $hash;
```

Example INSERT statements:
```sql
INSERT INTO users (name, email, password, phone, role) VALUES
('Receptionist', 'rec@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456789', 'receptionist'),
('Doctor', 'doc@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654321', 'doctor');
```

## Expected Folder Structure

```
opticlinic/
├── admin/
├── config/
├── controllers/
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   ├── init.php
│   └── shared_calendar.php
├── lang/
├── models/
├── receptionist/
├── doctor/
├── views/
├── .env.example
├── database.sql
└── README.md
```

## Notes

- `includes/config.php` loads database credentials from `$_ENV`, with fallback defaults if the variables are absent.
- If you use an `.env` file, it is parsed automatically by `includes/config.php` and its values are assigned into `$_ENV`.
- `includes/init.php` now centralizes the single `session_start()` call, so session handling is unified across the application.

# PureGlow

PureGlow is a PHP/MySQL skincare storefront with a customer website, checkout flow, admin dashboard, product management, bookings, contacts, subscriptions, promotions, and invoice emails.

## Requirements

- PHP 8+
- MySQL or MariaDB
- Apache with `mod_rewrite`/`.htaccess` support

## Local Setup

1. Copy the project into your web root, for example `C:\xampp\htdocs\skincare`.
2. Create a MySQL database named `pureglow`.
3. Import `database/schema.sql`.
4. Update database credentials in `config/config.php`.
5. Create the admin user:

```bash
php database/seed_admin.php "Admin User" "admin@pureglow.al" "Admin123!"
```

6. Open the site:

```text
http://localhost/skincare/
```

Admin panel:

```text
http://localhost/skincare/admin/login.php
```

## Hosting Checklist

- Import `database/schema.sql` on the hosting database.
- Update `config/config.php` with the hosting DB credentials.
- Create one admin user with `database/seed_admin.php` or phpMyAdmin.
- Make `uploads/products` writable if product image uploads are used.
- Keep `.htaccess` enabled so `config`, `models`, and `database` are not directly accessible from the browser.

## Git Notes

Runtime product uploads are ignored by Git, but the folder structure is kept with `uploads/products/.gitkeep`.

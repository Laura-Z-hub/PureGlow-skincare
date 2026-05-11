# PureGlow PHP Backend

This workspace now includes a complete PHP backend for the existing frontend, built to run on XAMPP with PHP 8 and MySQL.

## What was added
- `config/` for database connection and reusable request helpers
- `api/` for REST API endpoints: auth, products, cart, orders, contact, booking, newsletter, admin stats
- `models/` for PHP database models and domain logic
- `admin/` for a minimal admin login and dashboard
- `database/schema.sql` for database creation and product seed data

## Setup
1. Copy this project into your XAMPP `htdocs` folder or configure your Apache virtual host.
2. Create the MySQL database and tables by importing `database/schema.sql` in phpMyAdmin or MySQL CLI.
3. Update `config/config.php` if your MySQL credentials are not `root` with no password.
4. Create an admin user after importing the schema. You can either insert the hashed password manually or run the seeded helper script:

```bash
php database/seed_admin.php "Admin User" "admin@pureglow.al" "Admin123!"
```

If you do not have PHP CLI available, generate a password hash in a local PHP environment and insert it into the `users` table with role `admin`.

## API endpoints
- `POST /api/auth.php?action=login`
- `POST /api/auth.php?action=register`
- `POST /api/auth.php?action=logout`
- `GET /api/auth.php?action=status`
- `GET /api/products.php`
- `GET /api/products.php?id={id}`
- `GET /api/products.php?category={category}`
- `POST /api/products.php` (admin)
- `PUT /api/products.php?id={id}` (admin)
- `DELETE /api/products.php?id={id}` (admin)
- `GET /api/cart.php`
- `POST /api/cart.php`
- `PUT /api/cart.php`
- `DELETE /api/cart.php`
- `POST /api/orders.php`
- `GET /api/orders.php`
- `GET /api/orders.php?id={id}`
- `PUT /api/orders.php?id={id}` (admin)
- `POST /api/contact.php`
- `POST /api/booking.php`
- `POST /api/newsletter.php`
- `GET /api/admin.php` (admin)

## Admin panel
- `admin/login.php`
- `admin/dashboard.php`
- `admin/logout.php`

## Notes
- The existing frontend design in `index.html` was preserved.
- Contact, booking, and newsletter forms can now be backed by the new PHP REST API.
- Cart and order endpoints are available for future frontend integration.

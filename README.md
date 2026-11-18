# GymShop

Online store for gym supplements (creatine, protein, pre-workout, amino acids, vitamins), built with plain PHP 8 and MySQL using an MVC + Repository architecture, without a framework.

## Features

- Product catalog with categories, search, sorting and product details pages
- Image carousel on the home page and CSS animations
- User registration and login (passwords hashed with `password_hash`)
- Role based authorization: `customer` and `admin`
- Shopping cart stored in the session, checkout and order history
- Admin panel: product CRUD with image upload, order status management, dashboard
- Activity log: user actions are written to `storage/logs/activity.log` and can be read or downloaded from **Admin → Jurnal activitate**
- Responsive layout for desktop, tablet and phone
- Unit tests (PHPUnit) and GitHub Actions continuous integration

## Tech stack

- PHP 8.1+ (custom MVC), PDO with prepared statements
- MySQL 8 / MariaDB
- Vanilla JavaScript and CSS (4 stylesheets: `main`, `components`, `animations`, `responsive`)
- PHPUnit 10, GitHub Actions

## Project structure

```
app/
  Controllers/        HTTP controllers (Admin/ contains the admin area, restricted to admins)
  Core/               Router, Request, View, Session, Auth, Validator, Database, ErrorHandler
  Exceptions/         AppException and its subclasses (404, 403, 422, 500)
  Interfaces/         RepositoryInterface, LoggerInterface
  Models/             Model (abstract) and the entities: Product, Category, Brand, User, Order, OrderItem
  Repositories/       BaseRepository (abstract) and one repository per table
  Services/           Cart, FileLogger, ImageUploader
  Views/              PHP templates (layouts, partials, pages)
  bootstrap.php       autoloader, configuration, session
  routes.php          URL => controller action
config/               configuration (config.local.php is ignored by git)
database/             schema.sql (3NF) and seed.sql (demo data)
public/               web root: index.php (front controller), assets, uploads
storage/logs/         activity log
tests/Unit/           PHPUnit tests
```

## Database

The schema is in third normal form: `roles`, `users`, `categories`, `brands`, `products`, `orders`, `order_items`.
Order totals are not stored; they are calculated from `order_items`.

## Running locally (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) with PHP 8.1 or newer and start **Apache** and **MySQL**.
2. Copy the project into `C:\xampp\htdocs\gymshop`.
3. In phpMyAdmin create a database named `gymshop` (collation `utf8mb4_unicode_ci`), then import `database/schema.sql` and `database/seed.sql`.
4. Copy `config/config.example.php` to `config/config.local.php` and adjust the database credentials if needed.
5. Open `http://localhost/gymshop/`.

Demo accounts created by the seed file:

| Role     | Email               | Password   |
|----------|---------------------|------------|
| Admin    | admin@gymshop.test  | Admin123!  |
| Customer | client@gymshop.test | Client123! |

## Running the tests

```bash
composer install
composer test
```

## Continuous integration

`.github/workflows/ci.yml` runs on every push and pull request:

1. PHP syntax check and the PHPUnit test suite on PHP 8.1 and 8.3.
2. Imports the database into a MySQL 8 container, starts the application and runs `.github/scripts/smokeTest.sh`,
   which opens the main pages, simulates a customer order and an admin session, and checks the activity log.

## Deployment (free PHP + MySQL hosting, e.g. InfinityFree)

1. Create a hosting account and a MySQL database in the control panel.
2. Import `database/schema.sql` and `database/seed.sql` with the host's phpMyAdmin.
3. In the GitHub repository go to **Settings → Secrets and variables → Actions** and add:
   - secrets: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
   - variable: `DEPLOY_ENABLED` = `true` (optional: `FTP_SERVER_DIR`, default `/htdocs/`)
4. Every push to `main` that passes CI is uploaded automatically by `.github/workflows/deploy.yml`
   (it can also be started manually from the **Actions** tab).

The root `.htaccess` forwards every request to `public/` and blocks access to the application code, configuration, database scripts and logs.

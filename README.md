# GymShop

Online store for gym supplements (creatine, protein, pre-workout, vitamins), built with plain PHP 8 and MySQL using an MVC + Repository architecture.

## Tech stack

- PHP 8.1+ (no framework, custom MVC)
- MySQL / MariaDB (PDO with prepared statements)
- Vanilla JavaScript and CSS
- PHPUnit for unit tests
- GitHub Actions for continuous integration

## Running locally (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) (PHP 8.1 or newer) and start Apache and MySQL.
2. Clone the project into `C:\xampp\htdocs\gymshop`.
3. Open phpMyAdmin and import `database/schema.sql`, then `database/seed.sql`.
4. Copy `config/config.example.php` to `config/config.local.php` and adjust the database credentials.
5. Open `http://localhost/gymshop/`.

Demo accounts created by the seed file:

| Role     | Email               | Password    |
|----------|---------------------|-------------|
| Admin    | admin@gymshop.test  | Admin123!   |
| Customer | client@gymshop.test | Client123!  |

## Running the tests

```bash
composer install
composer test
```

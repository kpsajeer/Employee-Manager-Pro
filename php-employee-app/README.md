# Employee Manager

A production-quality PHP 8.3 CRUD application for managing employees.  
Built with pure PHP, MySQL, PDO, Bootstrap 5, and a clean MVC-inspired architecture — **no frameworks required**.

---

## Features

| Feature | Details |
|---|---|
| CRUD | List, Add, Edit, Delete, View |
| Search | By name or email (live filter) |
| Filter | By department |
| Sort | Any column, asc/desc |
| Pagination | 10 records per page |
| Dashboard | Total / Active / Inactive / Departments stats |
| CSV Export | Filtered export with BOM for Excel |
| Flash messages | Session-based success / error alerts |
| CSRF protection | Per-session token with `hash_equals` comparison |
| XSS protection | `htmlspecialchars` on all output |
| SQL injection | PDO prepared statements throughout |
| Form validation | Server-side with field-level error display |
| Responsive | Bootstrap 5 grid, mobile-friendly sidebar |

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.3+ |
| MySQL | 8.0+ (or MariaDB 10.5+) |
| Composer | 2.x |
| PHP extensions | `pdo`, `pdo_mysql`, `mbstring` |

---

## Installation

### 1 · Clone or copy the project

```bash
cp -r php-employee-app /your/projects/directory
cd /your/projects/directory/php-employee-app
```

### 2 · Install Composer dependencies

```bash
composer install
```

This generates the `vendor/` directory with PSR-4 autoloading.

### 3 · Create the database and import the schema

```bash
mysql -u root -p < database.sql
```

Or open `database.sql` in phpMyAdmin / TablePlus / DBeaver and execute it.  
The script creates the `employee_manager` database, the `employees` table, and seeds **20 sample employees**.

### 4 · Update database credentials

Open `config/database.php` and set your MySQL credentials:

```php
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'employee_manager',
    'username' => 'root',       // ← your MySQL username
    'password' => 'secret',     // ← your MySQL password
    'charset'  => 'utf8mb4',
    // ...
];
```

### 5 · Start the PHP built-in server

```bash
php -S localhost:8000 -t public
```

Open your browser at **http://localhost:8000**

---

## Project Structure

```
php-employee-app/
├── app/
│   ├── Controllers/
│   │   ├── BaseController.php     # Shared utilities (render, flash, CSRF, redirect, validation)
│   │   ├── DashboardController.php
│   │   └── EmployeeController.php # Full CRUD + CSV export
│   ├── Models/
│   │   ├── Database.php           # PDO singleton
│   │   └── Employee.php           # All DB queries (paginate, find, create, update, delete, export)
│   └── Views/
│       ├── layout/
│       │   ├── header.php         # HTML head + sidebar + topbar + flash messages
│       │   └── footer.php         # Bootstrap JS + modal/sidebar JS
│       ├── dashboard/
│       │   └── index.php          # Stat cards + quick actions + workforce chart
│       └── employees/
│           ├── index.php          # Searchable, filterable, sortable, paginated table
│           ├── create.php         # Add employee form
│           ├── edit.php           # Edit employee form
│           └── show.php           # Employee detail view
├── config/
│   ├── app.php                    # App name, pagination limit, departments, timezone
│   └── database.php               # DB host, port, name, credentials
├── public/
│   ├── index.php                  # Front controller + regex router
│   └── .htaccess                  # Apache mod_rewrite (optional – not needed for built-in server)
├── storage/                       # Writable directory for future file uploads / logs
├── vendor/                        # Composer-managed autoloader (git-ignored)
├── composer.json
├── database.sql                   # Schema + 20 seed employees
└── README.md
```

---

## Routes

| Method | URI | Action |
|---|---|---|
| GET | `/` | Dashboard |
| GET | `/employees` | Employee list (search, filter, sort, paginate) |
| GET | `/employees/create` | Add employee form |
| POST | `/employees/store` | Save new employee |
| GET | `/employees/{id}` | Employee detail |
| GET | `/employees/{id}/edit` | Edit employee form |
| POST | `/employees/{id}/update` | Save updated employee |
| POST | `/employees/{id}/delete` | Delete employee |
| GET | `/employees/export` | Download CSV (respects active filters) |

---

## Security Highlights

- **CSRF** – a per-session token is embedded in every mutating form and verified server-side with `hash_equals`.
- **XSS** – all output runs through `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **SQL injection** – every query uses PDO prepared statements with bound parameters; sort column is whitelisted.
- **Session hardening** – `HttpOnly`, `SameSite=Lax` cookie flags; secure flag enabled when HTTPS detected.
- **Input validation** – email format, phone regex, salary range, date format, department whitelist checked server-side before any DB write.

---

## Apache / Nginx (production)

For Apache, `public/.htaccess` already contains the `mod_rewrite` rules.  
For Nginx, add this to your server block:

```nginx
root /path/to/php-employee-app/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

---

## Extending the App

| Task | Where to start |
|---|---|
| Add a new field | `database.sql` → `Employee::buildBinds()` → form views |
| Add a new page/route | `public/index.php` (add route) → new Controller method → new View file |
| Change departments list | `config/app.php` → `departments` array |
| Change pagination size | `config/app.php` → `pagination_limit` |
| Add file uploads | `public/` for upload target, `storage/` for storage, extend `EmployeeController` |
| Add authentication | Extend `BaseController` with an auth guard; store user in session |

---

## License

MIT — free to use, modify, and distribute.

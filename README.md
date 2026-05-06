# Pixous HR Admin Portal

A complete HR Employee Management System built with **PHP MVC Architecture** and **MySQL**.

## Features

- **Admin Login** — Secure authentication with brute-force protection, CSRF tokens
- **Dashboard** — Stats, department charts, task overview, recent leave requests
- **Employee Management** — Full CRUD, activate/deactivate, photo upload with MIME validation
- **Leave Management** — Request, approve/reject with SweetAlert confirmations
- **Payroll** — Auto-generate payslips with PF/ESI/PT breakdowns
- **Task Monitoring** — Create, assign, track progress with priority system
- **Security** — SQL injection prevention (PDO prepared statements), XSS protection, input sanitization, CSRF tokens, security headers
- **Responsive** — Bootstrap 5, mobile/tablet/desktop compatible

## Theme

Navy Blue · Gold · Gray · White

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with mod_rewrite enabled
- PDO MySQL extension

## Installation

### 1. Database Setup

```bash
mysql -u root -p < sql/schema.sql
```

This creates the `pixous_hr` database with all tables and seed data (15 employees from your ESI/PF file).

### 2. Configure Database

Edit `config/database.php` — update these values:

```php
private string $host   = 'localhost';
private string $dbname = 'pixous_hr';
private string $user   = 'root';
private string $pass   = '';  // your MySQL password
```

### 3. Configure Base URL

Edit `config/app.php`:

```php
define('BASE_URL', '/hr-portal/');  // Adjust to match your setup
```

Also update `RewriteBase` in `.htaccess` to match.

### 4. Deploy

Copy the `hr-portal/` folder to your web server's document root (e.g., `htdocs/` or `www/`).

### 5. Set Permissions

```bash
chmod -R 755 assets/uploads/
```

### 6. Login

Open `http://localhost/hr-portal/` in your browser.

```
Username: admin
Password: admin123
```

## MVC Architecture

```
hr-portal/
├── config/           # Database & app configuration
│   ├── app.php
│   └── database.php
├── controllers/      # Request handling & business logic
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── EmployeeController.php
│   ├── LeaveController.php
│   ├── PayrollController.php
│   └── TaskController.php
├── models/           # Database queries (PDO prepared statements)
│   ├── BaseModel.php
│   ├── Employee.php
│   ├── LeaveRequest.php
│   ├── Payroll.php
│   ├── Task.php
│   └── User.php
├── views/            # Presentation layer (PHP templates)
│   ├── layouts/      # Shared header, footer, sidebar
│   ├── auth/         # Login page
│   ├── dashboard/    # Dashboard view
│   ├── employees/    # Employee CRUD views
│   ├── leaves/       # Leave management view
│   ├── payroll/      # Payroll & payslip views
│   └── tasks/        # Task monitoring view
├── helpers/          # Security & utility functions
│   └── functions.php
├── assets/           # Static files
│   ├── css/style.css
│   └── uploads/      # Employee photos
├── sql/              # Database schema + seed data
│   └── schema.sql
├── index.php         # Front controller / router
└── .htaccess         # URL rewriting & security
```

## Security Measures

| Threat              | Protection                                          |
|---------------------|-----------------------------------------------------|
| SQL Injection       | PDO prepared statements throughout                  |
| XSS                 | `htmlspecialchars()` via `e()` helper on all output  |
| CSRF                | Token generation & verification on all POST forms   |
| Brute Force         | Login attempt limiting with lockout                  |
| File Upload Attacks | MIME type validation using `finfo`, extension check  |
| Session Hijacking   | Secure session config, `session_regenerate_id()`     |
| Directory Traversal | `.htaccess` blocks sensitive files                   |
| Clickjacking        | `X-Frame-Options: SAMEORIGIN` header                |

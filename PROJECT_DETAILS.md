# College Store Management System

## Overview

The **College Store Management System** is a PHP/MySQL web application for managing a campus store. Its intended users are:

- **Students**, who can register, sign in, purchase campus supplies, and request printing services.
- **Administrators**, who can sign in, monitor store statistics, manage products and stock, and review orders.

The current repository contains the public landing page, administrator authentication, an administrator dashboard, product management, database schema, and shared styling. Several student-facing and order-management pages referenced by the UI or README are not currently present in the repository; these are documented in [Implementation status](#implementation-status).

## Technology stack

| Layer | Technology | Verified usage |
|---|---|---|
| Server-side application | PHP | PHP scripts process requests, sessions, database queries, redirects, and HTML rendering. |
| Database | MySQL | `database.sql` creates the `campus_store` database and its relational tables. |
| Database access | MySQLi extension | `config/db.php` opens the connection; prepared statements are used in login and product mutations. |
| Frontend markup | HTML5 | Pages use semantic HTML structure, forms, navigation, tables, and responsive metadata. |
| Styling | CSS3 | `style.css` provides layout, cards, forms, tables, alerts, dashboard grids, and responsive rules. |
| UI framework | Bootstrap 5.1.3 | Loaded from jsDelivr CDN for navigation, grid layout, cards, forms, tables, buttons, and alerts. |
| Client-side JavaScript | Bootstrap Bundle 5.1.3 | Loaded from jsDelivr CDN; used for Bootstrap interactive components such as the mobile navbar. |
| Authentication state | PHP sessions | `session_start()` and session variables protect administrator pages. |
| Password handling | PHP password API | Administrator login verifies stored hashes with `password_verify()`. |
| Web server/runtime | PHP-compatible web server | The code expects PHP with MySQLi enabled; Apache or another PHP-capable server can host it. |
| Dependency management | None detected | No Composer manifest, Node manifest, build system, or local frontend dependency bundle is present. |

## Architecture

The application uses a simple server-rendered architecture:

1. A browser requests a PHP page.
2. PHP starts or checks a session where required.
3. PHP loads the shared MySQL connection from `config/db.php`.
4. PHP executes SQL queries using MySQLi.
5. The script renders HTML, Bootstrap classes, and application CSS directly to the response.
6. Form submissions are handled by the same PHP page or by a linked PHP endpoint.

There is no separate REST API, SPA frontend, ORM, MVC framework, JavaScript application, or background worker in the current repository.

## Repository structure

```text
.
├── README.md
├── PROJECT_DETAILS.md
├── database.sql
├── index.php
├── style.css
├── config/
│   └── db.php
└── admin/
    ├── admin_login.php
    ├── dashboard.php
    └── manage_products.php
```

### `index.php`

The public Campus Store landing page:

- Displays the Campus Store brand and welcome message.
- Offers links for student login, student registration, and administrator login.
- Presents separate student and administrator call-to-action cards.
- Loads Bootstrap 5.1.3 CSS and JavaScript from jsDelivr.
- Loads the shared `style.css` stylesheet.

The student links currently point to `student/login.php` and `student/register.php`, but those files are not tracked in the repository.

### `config/db.php`

The shared database bootstrap file:

- Defines the database host, username, password, and database name.
- Connects to MySQL with `mysqli_connect()`.
- Stores the connection in `$conn`.
- Stops execution with a connection error if the connection fails.

Current configured defaults are:

```text
Host: localhost
User: root
Password: empty
Database: campus_store
```

These values are suitable only for a local development setup and should be replaced with environment-specific configuration before deployment.

### `admin/admin_login.php`

Administrator authentication page:

- Starts a PHP session.
- Accepts a username and password through a POST form.
- Uses a prepared statement to find the administrator by username.
- Uses `password_verify()` to compare the submitted password with the stored hash.
- Sets `admin_loggedin` and `admin_id` session values after successful authentication.
- Redirects authenticated administrators to `dashboard.php`.
- Displays invalid username, invalid password, and general database error messages.
- Escapes the current form action with `htmlspecialchars()`.

### `admin/dashboard.php`

Protected administrator dashboard:

- Redirects unauthenticated users to `admin_login.php`.
- Counts non-cancelled product orders.
- Counts non-cancelled printing orders.
- Counts products.
- Counts registered students.
- Retrieves the five most recent product orders with product and student names.
- Lists products with fewer than ten units.
- Marks products with fewer than five units as critical using a Bootstrap danger alert.
- Displays navigation links for product management, order viewing, and logout.

The dashboard references `view_orders.php` and `logout.php`, but neither file is currently present.

### `admin/manage_products.php`

Protected product and inventory management page:

- Redirects unauthenticated users to `admin_login.php`.
- Adds a product with name, price, and initial quantity.
- Updates the stock quantity for an existing product.
- Uses prepared statements for INSERT and UPDATE operations.
- Lists all products alphabetically by name.
- Shows product ID, name, price, current stock, and an inline stock update form.
- Displays success and error alerts.
- Uses server-side output escaping when rendering database values.

### `database.sql`

Database initialization script:

- Creates the `campus_store` database if it does not exist.
- Selects the database.
- Creates six relational tables.
- Adds foreign-key relationships from orders to students/products and printing orders to students.
- Inserts an administrator row containing a placeholder bcrypt-style hash.

### `style.css`

Shared CSS:

- Sets the global font and line height.
- Styles the navigation bar, content containers, forms, tables, cards, alerts, dashboard statistics, product grids, and product cards.
- Adds a responsive breakpoint at 768px.
- Changes dashboard statistics to a single-column layout on smaller screens.

## Database design

### `students`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `student_id` | `INT` | Primary key, auto-increment | Student identifier |
| `name` | `VARCHAR(100)` | Required | Student name |
| `email` | `VARCHAR(100)` | Required, unique | Login/contact email |
| `password` | `VARCHAR(255)` | Required | Password hash |
| `created_at` | `TIMESTAMP` | Defaults to current timestamp | Registration time |

### `admin`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `admin_id` | `INT` | Primary key, auto-increment | Administrator identifier |
| `username` | `VARCHAR(50)` | Required, unique | Administrator login name |
| `password` | `VARCHAR(255)` | Required | Password hash |
| `created_at` | `TIMESTAMP` | Defaults to current timestamp | Account creation time |

### `products`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `product_id` | `INT` | Primary key, auto-increment | Product identifier |
| `name` | `VARCHAR(100)` | Required | Product name |
| `price` | `DECIMAL(10,2)` | Required | Unit price |
| `quantity` | `INT` | Required | Available inventory |
| `created_at` | `TIMESTAMP` | Defaults to current timestamp | Product creation time |

### `orders`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `order_id` | `INT` | Primary key, auto-increment | Order identifier |
| `student_id` | `INT` | Foreign key to `students` | Ordering student |
| `product_id` | `INT` | Foreign key to `products` | Ordered product |
| `quantity` | `INT` | Required | Number of units |
| `total_price` | `DECIMAL(10,2)` | Required | Order total |
| `status` | `ENUM` | Pending/Processed/Cancelled | Fulfillment state |
| `payment_status` | `ENUM` | Paid/Pending | Payment state |
| `date` | `TIMESTAMP` | Defaults to current timestamp | Order time |

### `printing_orders`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `print_id` | `INT` | Primary key, auto-increment | Printing order identifier |
| `student_id` | `INT` | Foreign key to `students` | Requesting student |
| `file_path` | `VARCHAR(255)` | Required | Stored print-file path |
| `pages` | `INT` | Required | Number of pages |
| `total_cost` | `DECIMAL(10,2)` | Required | Printing cost |
| `status` | `ENUM` | Pending/Processed/Cancelled | Fulfillment state |
| `payment_status` | `ENUM` | Paid/Pending | Payment state |
| `date` | `TIMESTAMP` | Defaults to current timestamp | Request time |

## Current functionality

### Implemented

- Public landing page.
- Administrator login.
- Session-based administrator page protection.
- Password-hash verification for administrator login.
- Administrator dashboard metrics.
- Recent product-order query.
- Low-stock product alerts.
- Add-product workflow.
- Update-stock workflow.
- MySQL schema for students, administrators, products, product orders, and printing orders.
- Responsive Bootstrap-based layout.
- Shared responsive CSS.

### Described or referenced but not implemented in the current checkout

- Student registration.
- Student login.
- Student product ordering interface.
- Student printing-service submission interface.
- Automated order processing.
- Dynamic printing-cost calculation.
- Administrator logout.
- Administrator order-viewing page.
- Payment processing.
- Product deletion or editing beyond stock quantity.
- File upload handling for print documents.

## Setup and local execution

### Requirements

- PHP with the MySQLi extension.
- MySQL or MariaDB.
- A PHP-capable web server, such as Apache.
- Internet access if Bootstrap is expected to load from jsDelivr.

### Installation

1. Clone or copy the repository into the web server document root.
2. Create the database and tables:

   ```bash
   mysql -u root -p < database.sql
   ```

3. Update `config/db.php` with the local database host, user, password, and database name.
4. Start the PHP application through Apache or a PHP development server:

   ```bash
   php -S localhost:8000
   ```

5. Open `http://localhost:8000/index.php`.

The SQL script inserts an administrator record with the literal placeholder hash `$2y$10$YourHashedPasswordHere`; it does not provide a usable default password. A real bcrypt hash must be inserted before administrator login can succeed.

## Security and reliability notes

Positive implementation details:

- Administrator pages check the session before rendering protected content.
- Login, product insertion, and stock updates use prepared statements.
- Password verification uses PHP's password API rather than comparing plaintext values.
- Database-derived values are generally escaped before being placed into HTML.

Items that should be addressed before production use:

- Move database credentials out of source code and into environment-specific configuration.
- Replace `die()` database errors with controlled server-side logging and a user-safe error page.
- Add CSRF protection to all state-changing forms.
- Validate and constrain numeric input on the server, not only through HTML attributes.
- Add authorization and ownership checks to student and order workflows when implemented.
- Add session regeneration on successful login and a working logout endpoint.
- Configure secure cookie flags and HTTPS.
- Add rate limiting or lockout protection for login attempts.
- Replace the placeholder administrator hash with a generated `password_hash()` result.
- Handle database query failures explicitly on dashboard counting and list queries.
- Add transaction handling for operations that change both inventory and orders.
- Validate and safely store uploaded printing files before enabling file uploads.

## Implementation status

This document describes the code currently tracked in the repository, not only the feature list in the existing README. The existing README describes a broader full-stack system, including student authentication, order processing, printing, and Bootstrap-based responsive UI. Bootstrap and the administrator/inventory portions are present, while several of the described modules are absent from the current checkout.

The repository currently has no automated test suite, dependency manifest, CI workflow, or deployment configuration.

## License and project metadata

No license file is present in the current repository. The landing page footer displays `© 2025 Campus Store`, but this is application content and does not establish an open-source license.

# College Store Management System

## Overview

The **College Store Management System** is a server-rendered PHP/MySQL web application intended to manage a college campus store. The planned system supports two primary user groups:

- **Students**, who can register, log in, purchase campus supplies, and request printing services.
- **Administrators**, who can authenticate, monitor store activity, manage products and stock, and review orders.

The current repository contains the public landing page, administrator authentication, an administrator dashboard, product/inventory management, the MySQL schema, and shared CSS. The repository also contains references to student and order-management pages that are not currently tracked. This document describes the code that is actually present and clearly identifies incomplete or planned areas.

## Technology stack

| Layer | Technology | Verified usage |
|---|---|---|
| Server-side application | PHP | PHP scripts process requests, sessions, database queries, redirects, validation, and HTML rendering. |
| Database | MySQL | `database.sql` creates the `campus_store` database and six relational tables. |
| Database access | MySQLi extension | `config/db.php` creates the connection; prepared statements are used in administrator login and product mutations. |
| Frontend markup | HTML5 | PHP pages render HTML forms, navigation, tables, cards, and responsive viewport metadata. |
| Styling | CSS3 | `style.css` provides global styles, layouts, cards, alerts, dashboard grids, product cards, and responsive rules. |
| UI framework | Bootstrap 5.1.3 | Loaded from jsDelivr for the responsive navbar, grid, cards, forms, tables, buttons, and alerts. |
| Client-side JavaScript | Bootstrap Bundle 5.1.3 | Loaded from jsDelivr for Bootstrap interactive components such as the mobile navigation toggle. |
| Authentication state | PHP sessions | `session_start()` and administrator session variables protect dashboard and inventory pages. |
| Password handling | PHP password API | Administrator login uses `password_verify()` against a stored password hash. |
| Output escaping | PHP built-in escaping | Database values and form actions are rendered with `htmlspecialchars()` in the inspected pages. |
| Web server/runtime | PHP-compatible web server | The code expects PHP with MySQLi enabled and can run through Apache or another PHP-capable server. |
| Dependency management | None detected | No Composer manifest, Node manifest, build pipeline, local vendor directory, or package manager configuration is tracked. |

There is no evidence in the current repository of a PHP framework, ORM, REST API, SPA frontend, payment gateway, external authentication provider, automated deployment pipeline, or JavaScript application framework.

## Application architecture

The project uses a straightforward server-rendered architecture:

1. A browser requests a PHP page.
2. The PHP script starts or checks a session where required.
3. The script includes `config/db.php` to obtain the MySQL connection.
4. PHP executes SQL statements using MySQLi.
5. The script renders HTML with Bootstrap classes and the shared stylesheet.
6. Form submissions are handled by PHP POST branches in the receiving page.

The application is organized as a small collection of page-oriented PHP scripts rather than a framework-based MVC application. Business logic, SQL access, session checks, and HTML rendering are colocated in the relevant pages.

## Repository structure

```text
.
├── .gitignore
├── .DS_Store
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

Only the files shown above are tracked in the current repository. The public page and administrator pages link to additional paths, but those target files are not present in the checkout.

## Page and file details

### `index.php`

The public Campus Store landing page:

- Sets the document language to English.
- Includes responsive viewport metadata.
- Displays the `Campus Store` brand.
- Provides navigation links for student login, student registration, and administrator login.
- Presents separate student and administrator call-to-action cards.
- Describes the store as a source for campus supplies and printing services.
- Loads Bootstrap 5.1.3 CSS from jsDelivr.
- Loads the shared `style.css` file.
- Loads the Bootstrap 5.1.3 JavaScript bundle from jsDelivr.
- Displays a footer containing `© 2025 Campus Store`.

The page links to:

```text
student/login.php
student/register.php
admin/admin_login.php
```

Only the administrator login target exists in the repository.

### `config/db.php`

The shared MySQL connection file:

- Defines `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_NAME`.
- Uses `localhost` as the database server.
- Uses `root` with an empty password by default.
- Connects to the `campus_store` database with `mysqli_connect()`.
- Stores the connection in `$conn`.
- Stops execution and prints a connection error if the connection fails.

Current development defaults are:

```text
Host: localhost
Username: root
Password: empty
Database: campus_store
```

These are local-development defaults and should not be used unchanged in a production deployment.

### `admin/admin_login.php`

Administrator authentication page:

- Starts a PHP session.
- Includes the shared database connection.
- Accepts a username and password through a POST form.
- Escapes the submitted username with `mysqli_real_escape_string()`.
- Uses a prepared statement to query the administrator by username.
- Uses `mysqli_stmt_bind_param()` to bind the username.
- Uses `password_verify()` to compare the submitted password with the stored hash.
- Stores `admin_loggedin` and `admin_id` in the session after successful authentication.
- Redirects authenticated users to `dashboard.php`.
- Reports invalid usernames, invalid passwords, and a general database error.
- Escapes the form action with `htmlspecialchars()`.
- Loads Bootstrap and shared CSS for the login interface.

The login flow expects the `admin.password` column to contain a valid password hash.

### `admin/dashboard.php`

Protected administrator dashboard:

- Starts a session and includes the database connection.
- Redirects users to `admin_login.php` unless `admin_loggedin` is exactly `true`.
- Counts product orders whose status is not `Cancelled`.
- Counts printing orders whose status is not `Cancelled`.
- Counts all products.
- Counts all registered students.
- Retrieves the five most recent product orders.
- Joins orders with products and students to display product and student names.
- Lists products with fewer than ten units.
- Uses a danger alert for products with fewer than five units.
- Uses a warning alert for products with five to nine units.
- Displays summary cards for active orders, print orders, products, and students.
- Displays recent order ID, student, product, amount, and status.
- Provides links to product management, order viewing, and logout.

The page references `view_orders.php` and `logout.php`, but those files are not tracked in the current repository.

### `admin/manage_products.php`

Protected inventory-management page:

- Starts a session and includes the database connection.
- Redirects unauthenticated users to `admin_login.php`.
- Handles an `add_product` POST action.
- Reads product name, price, and initial quantity.
- Uses a prepared INSERT statement for new products.
- Handles an `update_stock` POST action.
- Reads a product ID and replacement quantity.
- Uses a prepared UPDATE statement for stock changes.
- Displays success or error alerts.
- Retrieves all products ordered alphabetically by name.
- Displays product ID, name, price, and current stock.
- Provides an inline stock update form for every product.
- Escapes database values before rendering them in HTML.
- Uses Bootstrap cards, forms, tables, and buttons.

The page supports adding products and changing stock quantities, but it does not implement product deletion or general product editing.

### `database.sql`

The database initialization script:

- Creates the `campus_store` database if it does not exist.
- Selects `campus_store`.
- Creates the `students` table.
- Creates the `admin` table.
- Creates the `products` table.
- Creates the `orders` table.
- Creates the `printing_orders` table.
- Adds foreign keys from orders to students and products.
- Adds a foreign key from printing orders to students.
- Inserts one administrator record containing a placeholder hash.

### `style.css`

The shared stylesheet:

- Sets Arial as the primary font.
- Defines global line height, margin, and padding.
- Adds navigation bar padding and a subtle box shadow.
- Adds consistent container padding.
- Styles centered form containers.
- Styles form groups, tables, cards, card headers, and alerts.
- Defines dashboard statistic cards using a responsive CSS grid.
- Defines product-grid and product-card styles for future or additional product views.
- Changes the dashboard statistics to one column below 768px.

## Database design

### Entity relationship summary

```text
students 1 ──── * orders * ──── 1 products
students 1 ──── * printing_orders
```

Administrators are independent authentication records and are not connected by a foreign key to store operations in the current schema.

### `students`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `student_id` | `INT` | Primary key, auto-increment | Student identifier |
| `name` | `VARCHAR(100)` | Required | Student name |
| `email` | `VARCHAR(100)` | Required, unique | Student email/login identity |
| `password` | `VARCHAR(255)` | Required | Student password hash |
| `created_at` | `TIMESTAMP` | Defaults to current timestamp | Registration time |

### `admin`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `admin_id` | `INT` | Primary key, auto-increment | Administrator identifier |
| `username` | `VARCHAR(50)` | Required, unique | Administrator login name |
| `password` | `VARCHAR(255)` | Required | Administrator password hash |
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
| `order_id` | `INT` | Primary key, auto-increment | Product-order identifier |
| `student_id` | `INT` | Foreign key to `students` | Student who placed the order |
| `product_id` | `INT` | Foreign key to `products` | Product being ordered |
| `quantity` | `INT` | Required | Number of units |
| `total_price` | `DECIMAL(10,2)` | Required | Total order price |
| `status` | `ENUM` | `Pending`, `Processed`, or `Cancelled` | Fulfillment state |
| `payment_status` | `ENUM` | `Paid` or `Pending` | Payment state |
| `date` | `TIMESTAMP` | Defaults to current timestamp | Order creation time |

### `printing_orders`

| Column | Type | Constraints | Purpose |
|---|---|---|---|
| `print_id` | `INT` | Primary key, auto-increment | Printing-order identifier |
| `student_id` | `INT` | Foreign key to `students` | Student who requested printing |
| `file_path` | `VARCHAR(255)` | Required | Location of the print file |
| `pages` | `INT` | Required | Number of pages |
| `total_cost` | `DECIMAL(10,2)` | Required | Total printing cost |
| `status` | `ENUM` | `Pending`, `Processed`, or `Cancelled` | Fulfillment state |
| `payment_status` | `ENUM` | `Paid` or `Pending` | Payment state |
| `date` | `TIMESTAMP` | Defaults to current timestamp | Request creation time |

## Functional capabilities

### Implemented in the current checkout

- Public Campus Store landing page.
- Administrator username/password login.
- Session-based protection for administrator pages.
- Password-hash verification for administrator authentication.
- Administrator dashboard summary metrics.
- Recent product-order display.
- Low-stock inventory alerts.
- Product creation.
- Product stock updates.
- Relational MySQL schema for students, administrators, products, orders, and printing orders.
- Responsive Bootstrap navigation and layout.
- Shared responsive CSS.

### Schema-supported or referenced but not implemented here

- Student registration.
- Student login.
- Student account management.
- Student product catalog and ordering pages.
- Student printing-request form.
- Printing-file upload processing.
- Dynamic printing-cost calculation.
- Administrator order-management page.
- Administrator logout endpoint.
- Order status update workflow.
- Payment collection or payment-gateway integration.
- Product deletion.
- Product name/price editing.
- Inventory reservation or transactional stock deduction.

The README describes a broader feature set than the current tracked files implement. The missing areas should be treated as planned, incomplete, or removed functionality until corresponding source files are added.

## Main page flow

### Public flow

```text
GET /
  ├── Student Login       -> student/login.php (missing)
  ├── Student Register    -> student/register.php (missing)
  └── Admin Login         -> admin/admin_login.php
```

### Administrator flow

```text
POST admin/admin_login.php
  └── successful login -> admin/dashboard.php

admin/dashboard.php
  ├── Manage Products -> admin/manage_products.php
  ├── View Orders     -> admin/view_orders.php (missing)
  └── Logout          -> admin/logout.php (missing)
```

### Product-management flow

```text
POST manage_products.php?add_product
  └── INSERT INTO products

POST manage_products.php?update_stock
  └── UPDATE products SET quantity = ...
```

## Setup and local execution

### Requirements

- PHP with the MySQLi extension enabled.
- MySQL or MariaDB.
- Apache, Nginx with PHP-FPM, or PHP's built-in development server.
- Internet access if Bootstrap is loaded from jsDelivr.

### Database setup

From the repository root:

```bash
mysql -u root -p < database.sql
```

If the local MySQL account has no password:

```bash
mysql -u root < database.sql
```

### Configuration

Update `config/db.php` for the local environment:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'campus_store');
```

Do not commit real production credentials to this file.

### Run with PHP's development server

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000/index.php
```

### Administrator seed data

The SQL script inserts:

```text
Username: admin
Password: placeholder; no usable password is supplied
```

The stored value is the literal placeholder:

```text
$2y$10$YourHashedPasswordHere
```

This is not a valid known password hash for a usable default account. Generate a real hash with PHP before attempting administrator login:

```bash
php -r "echo password_hash('your-secure-password', PASSWORD_DEFAULT), PHP_EOL;"
```

Then update the inserted administrator row in the database.

## Security and reliability assessment

### Positive implementation details

- Protected administrator pages verify the session before displaying content.
- Login uses a prepared statement for the username query.
- Product insertion uses a prepared statement.
- Stock updates use a prepared statement.
- Administrator passwords are checked with `password_verify()`.
- Database values are generally escaped before being placed into HTML.
- Database relationships are represented with foreign keys.
- Unique constraints protect student email and administrator username values.
- HTML forms use required fields and numeric input attributes for basic browser-side validation.

### Recommended improvements before production

- Move database credentials into environment variables or a server-only configuration file.
- Replace `die()` database errors with server-side logging and a user-safe error page.
- Add CSRF tokens to every state-changing form.
- Validate all numeric fields server-side, including positive prices, non-negative quantities, and valid IDs.
- Validate that submitted product IDs exist before updating stock.
- Add authorization checks for every administrator action.
- Regenerate the session ID after successful login.
- Add a logout endpoint that destroys the session securely.
- Configure secure, HTTP-only, same-site session cookies and HTTPS.
- Add login rate limiting or lockout protection.
- Replace the placeholder administrator password hash.
- Handle all `mysqli_query()` failures explicitly on the dashboard.
- Use a transaction when placing an order and reducing product inventory.
- Prevent stock from becoming negative during concurrent orders.
- Add upload validation, storage isolation, file-size limits, and malware scanning before enabling print-file uploads.
- Avoid exposing detailed database errors to end users.
- Add indexes for frequently queried foreign keys and order dates if the data volume grows.
- Add automated tests for login, authorization, stock updates, order creation, and printing workflows.

## Testing and quality status

The current repository does not contain:

- A PHPUnit or other automated test suite.
- A Composer manifest.
- A Node/npm manifest.
- A CI workflow.
- A Docker configuration.
- A deployment manifest.
- A local frontend build pipeline.

The documentation file itself was checked for Git whitespace errors with `git diff --check`. No application test command is currently defined by the repository.

## Project status

The repository is a small academic or prototype-style PHP/MySQL application with a working foundation for administrator inventory management. Its database schema anticipates a larger student ordering and printing system, but the corresponding student-facing and order-processing source files are not part of the current checkout.

The existing README presents the project as a broader full-stack campus-store solution. For accurate implementation reporting, distinguish between:

1. **Verified code**: public landing page, administrator authentication, dashboard metrics, product creation, stock updates, schema, Bootstrap, and CSS.
2. **Schema-supported design**: students, product orders, printing orders, statuses, and payment-status fields.
3. **Referenced or described functionality**: student authentication, printing submission, order processing, automated billing, order viewing, and logout.

No license file is present. The footer copyright text is application content and does not establish an open-source license.

# PHP Todo App — Local Development with WAMP

**Author:** [Soham Patil]  
**Date:** June 9, 2025  
**Stack:** WAMP (Windows + Apache + MySQL + PHP 8.5)  
**Editor:** VS Code with PHP Intelephense

---




### 1. WampServer Installation and Configuration

- Installed WampServer and verified all services (Apache, MySQL) were running via the system tray icon
- Confirmed the Apache welcome page loaded at `http://localhost/`
- Accessed phpMyAdmin at `http://localhost/phpmyadmin` using default credentials (`root` / no password)
- Located and understood the purpose of key WAMP directories:

| Directory | Purpose |
|---|---|
| `C:\wamp64\www\` | Web root — all projects live here |
| `C:\wamp64\bin\apache\` | Apache web server binaries and config |
| `C:\wamp64\bin\php\` | PHP engine (php8.5.0) |
| `C:\wamp64\bin\mysql\` | MySQL database engine |
| `C:\wamp64\logs\` | Apache and PHP error logs |

### 2. VS Code Configuration

- Installed PHP Intelephense extension for autocomplete and error highlighting
- Configured `settings.json` to point VS Code at the correct PHP binary:

```json
{
    "php.validate.executablePath": "C:\\wamp64\\bin\\php\\php8.5.0\\php.exe",
    "php.suggest.basic": false,
    "files.autoSave": "afterDelay"
}
```

- Understood the difference between **User settings** (global) and **Workspace settings** (per-project via `.vscode/settings.json`) — the correct approach when working across multiple stacks (WAMP, XAMPP)

### 3. Virtual Host (vhost) Configuration

Configured Apache to serve the project at `http://todo.test/` instead of `http://localhost/todo/`.

**Steps performed:**

1. Enabled vhost includes in `httpd.conf`:
   ```apache
   Include conf/extra/httpd-vhosts.conf
   ```

2. Added a vhost block to `httpd-vhosts.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName todo.test
       DocumentRoot "C:/wamp64/www/todo"
       <Directory "C:/wamp64/www/todo">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       ErrorLog  "C:/wamp64/logs/todo-error.log"
       CustomLog "C:/wamp64/logs/todo-access.log" combined
   </VirtualHost>
   ```

3. Added a DNS entry to the Windows hosts file (`C:\Windows\System32\drivers\etc\hosts`):
   ```
   127.0.0.1   todo.test
   ```

4. Restarted Apache for changes to take effect

**Why this matters:** In production, a virtual host is how a server like Nginx or Apache maps a domain name (`myapp.com`) to a specific folder on disk. The local setup is structurally identical.

### 4. MySQL Database Setup

- Created a database `todo_app` with collation `utf8mb4_unicode_ci` via phpMyAdmin
- Created a `tasks` table with the following schema:

```sql
CREATE TABLE tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    is_done     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Concepts demonstrated:**
- `AUTO_INCREMENT` — database-generated unique IDs (used in every real application)
- `TINYINT(1)` — MySQL idiom for a boolean flag
- `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` — automatic audit timestamp on every row

---

## Application Built — PHP Todo App

A simple but complete CRUD web application allowing users to add tasks, mark them done/undone, and delete them.

### Project Structure

```
C:\wamp64\www\todo\
├── config/
│   └── db.php          ← Database connection (PDO)
├── index.php           ← Main page: reads and displays all tasks
├── add.php             ← Handles POST request to insert a new task
├── delete.php          ← Handles POST requests to toggle or delete a task
└── .htaccess           ← Apache URL rewriting rules
```

### Features Implemented

- View all tasks ordered by creation date
- Add a new task via a form
- Mark a task as done / undo it (toggles `is_done` in the database)
- Delete a task permanently
- Per-vhost error logging for easier debugging

---

## Industry Concepts Demonstrated

Every pattern used in this app maps directly to how professional PHP applications work.

| Pattern used in this app | Industry equivalent |
|---|---|
| `config/db.php` with a `getDB()` function | Service container / dependency injection |
| PDO with named placeholders (`:title`, `:id`) | ORM parameterized queries (Eloquent, Doctrine) |
| `htmlspecialchars()` on all output | Template engine auto-escaping (Blade, Twig) |
| Separate `add.php` and `delete.php` handler files | Controllers in MVC architecture |
| `header('Location: index.php')` after POST | Post/Redirect/Get (PRG) pattern |
| `.htaccess` rewriting all requests to `index.php` | Front controller pattern (used by Laravel, Symfony) |
| Virtual host `todo.test` | Production domain pointing to `/var/www/myapp` |
| Per-vhost `ErrorLog` | Per-service log aggregation (Datadog, CloudWatch) |

### Key Security Practices Applied

- **Prepared statements** — all SQL queries use PDO placeholders, making SQL injection impossible
- **`htmlspecialchars()`** — all user-supplied data is escaped before being rendered in HTML, preventing XSS attacks
- **`(int)` casting on IDs** — user-supplied IDs are cast to integers as a second layer of defense before hitting the database
- **`REQUEST_METHOD` check** — handler files verify the request is a POST before doing anything

---

## How the Request Lifecycle Works

```
Browser types http://todo.test/
       ↓
Windows hosts file → resolves todo.test to 127.0.0.1
       ↓
Apache receives the request on port 80
       ↓
Apache matches the ServerName to the todo vhost
       ↓
Apache looks for index.php inside DocumentRoot
       ↓
PHP executes index.php → queries MySQL → builds HTML
       ↓
Apache sends the HTML response back to the browser
```

---

## Key Configuration Files Reference

| File | What it controls |
|---|---|
| `C:\wamp64\bin\apache\...\conf\httpd.conf` | Main Apache config |
| `C:\wamp64\bin\apache\...\conf\extra\httpd-vhosts.conf` | Virtual host definitions |
| `C:\wamp64\bin\php\php8.5.0\php.ini` | PHP settings (error reporting, upload limits, timezone) |
| `C:\Windows\System32\drivers\etc\hosts` | Local DNS — maps custom domains to 127.0.0.1 |
| `todo\.vscode\settings.json` | VS Code workspace settings for this project |

---

## Learnings and Takeaways

- WAMP is a self-contained local server stack. The `www` folder is the web root — every project is a subfolder inside it.
- Apache, PHP, and MySQL are three separate services. Apache handles HTTP, PHP executes code, MySQL stores data. They are loosely coupled and can be replaced independently (e.g. swap MySQL for PostgreSQL, swap Apache for Nginx).
- Virtual hosts are the mechanism by which one server handles multiple domains — locally or in production.
- PDO prepared statements are non-negotiable for any SQL that involves user input.
- The PRG pattern (redirect after POST) is a fundamental web pattern that prevents duplicate form submissions.
- The `.htaccess` front controller pattern is the foundation every major PHP framework is built on.

---

*This project was built as a learning exercise to understand the WAMP stack, Apache configuration, PHP-MySQL integration, and core web development patterns.*
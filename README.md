# Vending Machine PHP Application

A PHP application for a vending machine system with product management, inventory tracking, purchase transactions, and user authentication (sessions for web, JWT for API).

## Features

- **Product management**: CRUD for products (ID, name, price, quantity). Admin-only for create/edit/delete.
- **Inventory**: Track quantity; purchases decrement stock and log transactions.
- **Authentication**: Session-based for web (Admin/User roles); JWT for REST API.
- **Role-based access**: Admin can manage products; users can list and purchase.
- **Validation**: Server-side and client-side (required fields, positive price, non-negative quantity).
- **Pagination & sorting**: Product list supports `page`, `per_page`, `sort`, `dir`.
- **REST API**: JSON API for products and purchase with JWT auth.

## Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.2+
- Composer

## Setup

1. **Install dependencies**

   ```bash
   composer install
   ```

2. **Database**

   Create a database and run the schema:

   ```bash
   mysql -u root -p your_db < database/schema.sql
   ```

   Or set env vars and run manually:

   - `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` (see `configs/config.php`).
   - Default DB name: `vending_machine`.

3. **Web server**

   Point document root to `public/`:

   - PHP built-in: `php -S localhost:8000 -t public`
   - Apache/Nginx: set document root to `public`, URL rewriting so all requests go to `public/index.php`.

4. **Default logins** (see `database/schema.sql` and `docs/DATABASE_SETUP.md`)

   - Admin: `admin` / `password`
   - User: `mgmg` / `password`

## Routes (Web)

| Method | Route | Description |
|--------|--------|-------------|
| GET | `/login` | Login form |
| POST | `/login` | Login |
| GET | `/logout` | Logout |
| GET | `/products` | List products (pagination, sort) |
| GET | `/products/{id}` | Show product |
| GET | `/products/{id}/purchase` | Purchase form (attribute-style URL) |
| POST | `/products/{id}/purchase` | Process purchase |
| GET | `/products/create` | Create form (Admin) |
| POST | `/products/create` | Create product (Admin) |
| GET | `/products/{id}/edit` | Edit form (Admin) |
| POST | `/products/{id}/update` | Update product (Admin) |
| GET | `/products/{id}/delete` | Delete product (Admin) |

## REST API

Base path: `/api`. Use JWT for protected endpoints.

**Get token**

```http
POST /api/auth/login
Content-Type: application/json

{"username": "admin", "password": "password"}
```

Response: `{"token": "...", "type": "Bearer"}`

**Use token**

```http
Authorization: Bearer <token>
```

**Endpoints**

- `GET /api/products` — List (query: `page`, `per_page`, `sort`, `dir`)
- `GET /api/products/{id}` — Show one
- `POST /api/products` — Create (Admin, JSON body)
- `PUT /api/products/{id}` — Update (Admin, JSON body)
- `DELETE /api/products/{id}` — Delete (Admin)
- `POST /api/products/{id}/purchase` — Purchase (JSON: `{"quantity": 1}`)

## Tests

```bash
./vendor/bin/phpunit tests/
```

Tests use dependency injection and mocks so controller actions are tested without a real database.

## Project structure

- `configs/` — config (database, JWT)
- `database/schema.sql` — MySQL schema and seed data
- `docs/DATABASE_SETUP.md` — Database design and setup
- `public/index.php` — Front controller (web + API routes)
- `routes/web.php` — Web routes
- `routes/api.php` — API routes
- `src/` — Application code (Auth, Controllers, Database, Middleware, Models, Repositories, Validation, Router)
- `views/` — PHP views for web UI
- `tests/` — PHPUnit tests

# Database Setup for Vending Machine System

## Overview

The application uses **PHP** with **MySQL** and **PDO** for database access. Below is the schema design and relationship description.

## Tables

### 1. `products`

Stores vending machine products.

| Column             | Type         | Constraints                    | Description                    |
|--------------------|--------------|--------------------------------|--------------------------------|
| id                 | INT          | PRIMARY KEY, AUTO_INCREMENT    | Product ID                     |
| name               | VARCHAR(255) | NOT NULL                       | Product name                   |
| price              | DECIMAL(10,3)| NOT NULL, >= 0                 | Price in USD                   |
| quantity | INT          | NOT NULL, >= 0                 | Stock quantity                 |
| created_at         | DATETIME     | DEFAULT CURRENT_TIMESTAMP      | Creation time                  |
| updated_at         | DATETIME     | ON UPDATE CURRENT_TIMESTAMP    | Last update time               |

**Indexes:** `name` (for search/sort), `price`, `quantity`.

### 2. `users`

Stores system users (Admin and regular User).

| Column      | Type         | Constraints                    | Description                    |
|-------------|--------------|--------------------------------|--------------------------------|
| id          | INT          | PRIMARY KEY, AUTO_INCREMENT   | User ID                        |
| username    | VARCHAR(100) | NOT NULL, UNIQUE               | Login username                 |
| password    | VARCHAR(255) | NOT NULL                       | Hashed password (bcrypt)       |
| role        | ENUM         | NOT NULL, DEFAULT 'user'       | 'admin' or 'user'              |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP      | Creation time                  |
| updated_at  | DATETIME     | ON UPDATE CURRENT_TIMESTAMP    | Last update time               |

**Indexes:** `username` (UNIQUE), `role`.

### 3. `transactions`

Logs purchase transactions.

| Column       | Type         | Constraints                    | Description                    |
|--------------|--------------|--------------------------------|--------------------------------|
| id           | INT          | PRIMARY KEY, AUTO_INCREMENT   | Transaction ID                 |
| product_id   | INT          | NOT NULL, FK → products.id    | Product purchased              |
| user_id      | INT          | NULL, FK → users.id            | User who purchased (if logged in) |
| quantity     | INT          | NOT NULL, > 0                  | Quantity purchased             |
| total | DECIMAL(10,3)| NOT NULL                       | Total price                    |
| created_at   | DATETIME     | DEFAULT CURRENT_TIMESTAMP      | Purchase time                  |

**Foreign keys:**
- `product_id` → `products(id)` ON DELETE RESTRICT
- `user_id` → `users(id)` ON DELETE SET NULL

**Indexes:** `product_id`, `user_id`, `created_at`.

## Relationships

- **products** ↔ **transactions**: One-to-many (one product, many transactions).
- **users** ↔ **transactions**: One-to-many (one user, many transactions).
- No direct product–user relationship; link is through `transactions`.

## Seed Data

Initial products:

- Coke – 3.99 USD
- Pepsi – 6.885 USD  
- Water – 0.5 USD

Default users (change passwords in production):

- Username: `admin`, Role: `admin`, default password: `password`
- Username: `user`, Role: `user`, default password: `password`

Run the SQL file `database/schema.sql` to create tables and seed data.

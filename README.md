# Bookly

Bookly is a Laravel web application for selling and buying books. The app supports three roles: customers, sellers and admins. This README focuses on the application's web & API surface (controllers, routes, models, views), the file structure and the overall architecture.

## Quick links

- Routes: [routes/web.php](routes/web.php), [routes/api.php](routes/api.php)  
- Main web controller: [`App\Http\Controllers\DashboardController`](app/Http/Controllers/DashboardController.php)  
- Route provider: [`App\Providers\RouteServiceProvider`](app/Providers/RouteServiceProvider.php)  
- Core models: [`App\Models\Book`](app/Models/Book.php), [`App\Models\Category`](app/Models/Category.php), [`App\Models\Order`](app/Models/Order.php), [`App\Models\OrderItem`](app/Models/OrderItem.php), [`App\Models\SellerProfile`](app/Models/SellerProfile.php), [`App\Models\User`](app/Models/User.php), [`App\Models\Wishlist`](app/Models/Wishlist.php)  
- Views directory: [resources/views](resources/views)  
- App config: [config/app.php](config/app.php)  
- Tests config: [phpunit.xml](phpunit.xml)  
- Front-end / package manifest: [package.json](package.json)

## Overview & Features

- Roles
  - Admin: manage users, approve sellers and manage book categories (admin routes under `admin/*`). See routes in [routes/web.php](routes/web.php) and API admin group in [routes/api.php](routes/api.php).
  - Seller: maintain seller profile, create & manage books, view seller orders. Seller routes are grouped under `seller/*` in both web and API routes. Example controller namespaces are visible in [routes/web.php](routes/web.php).
  - Customer: browse shop, view book pages, add books to wishlist, create orders and view past orders. Shop pages and checkout flows are wired via web controllers (see [routes/web.php](routes/web.php)) and API endpoints (see [routes/api.php](routes/api.php)).

## Architecture & Patterns

- MVC structure (Laravel native)
  - Models live under `app/Models` (e.g. [`App\Models\Book`](app/Models/Book.php)). Models implement relationships and query scopes used by controllers and views.
  - Controllers under `app/Http/Controllers` and sub-namespaces (Admin, Seller, Customer, Shop, Api). Example: [`App\Http\Controllers\DashboardController`](app/Http/Controllers/DashboardController.php) determines which dashboard to show based on the authenticated user's role.
  - Views under `resources/views` – Blade templates render the public shop, dashboards, seller forms and admin pages.
- Routes
  - Web routes: [routes/web.php](routes/web.php) – handles browser pages and form submissions.
  - API routes: [routes/api.php](routes/api.php) – JSON endpoints used by the SPA or third-party clients; protected routes use `auth:sanctum`.
  - Role based groups: routes use middleware like `auth`, `role:admin`, `role:seller`, `role:customer` to restrict access (see groups in [routes/web.php](routes/web.php) and [routes/api.php](routes/api.php)).
  - Route binding and configuration is centralized via [`App\Providers\RouteServiceProvider`](app/Providers/RouteServiceProvider.php).
- Authorization & Authentication
  - Uses Laravel auth scaffolding and middleware across routes. API uses Sanctum for token-based auth (`auth:sanctum` in [routes/api.php](routes/api.php)).
  - Roles are stored on the `users` table and used in middleware checks.

## Important Controllers & Where to Look

- Dashboard & role-routing: [`App\Http\Controllers\DashboardController`](app/Http/Controllers/DashboardController.php) — routes users to admin/seller flows or redirects customers to the shop.
- Admin controllers (examples referenced in routes):
  - `App\Http\Controllers\Admin\CategoryController` — category CRUD (linked in [routes/web.php](routes/web.php))
  - `App\Http\Controllers\Admin\UserController`, `App\Http\Controllers\Admin\SellerController` — user & seller management
- Seller controllers:
  - `App\Http\Controllers\Seller\ProfileController` — seller profile view/edit
  - `App\Http\Controllers\Seller\BookController` — seller book CRUD
- Customer controllers:
  - `App\Http\Controllers\Customer\CheckoutController` — checkout flow
  - `App\Http\Controllers\Customer\OrderController` / `PaymentController` — order creation & payment flows
- API controllers: See imports at the top of [routes/api.php](routes/api.php) such as `App\Http\Controllers\Api\BookController`, `CategoryController`, `OrderController`, `SellerProfileController`, `WishlistController`.

(Exact controller files are referenced by the namespace imports in [routes/web.php](routes/web.php) and [routes/api.php](routes/api.php).)

## Models & Data Relationships

- Core models live in `app/Models`. The dashboard imports many of them in [`App\Http\Controllers\DashboardController`](app/Http/Controllers/DashboardController.php):
  - [`App\Models\Book`](app/Models/Book.php) — book attributes, status, stock and seller relations.
  - [`App\Models\Category`](app/Models/Category.php) — categories for books.
  - [`App\Models\Order`](app/Models/Order.php) and [`App\Models\OrderItem`](app/Models/OrderItem.php) — orders, line items and revenue calculations.
  - [`App\Models\SellerProfile`](app/Models/SellerProfile.php) — seller-specific metadata and approval status.
  - [`App\Models\User`](app/Models/User.php) — users with `role` attribute (admin, seller, customer).
  - [`App\Models\Wishlist`](app/Models/Wishlist.php) — customer wishlists.
- Use Eloquent relationships (hasMany, belongsTo) to navigate between users, books, orders and seller profiles.

## Routes & Example Endpoints

- Public shop pages (web):
  - GET /shop — [routes/web.php](routes/web.php)
  - GET /shop/books/{book:slug} — public book page
- Auth & user flows:
  - POST /login, POST /register, POST /logout — managed via auth controllers referenced in [routes/web.php](routes/web.php)
- Customer API (protected by Sanctum):
  - GET /api/me, POST /api/logout, GET /api/orders, POST /api/orders — see [routes/api.php](routes/api.php)
  - Wishlist endpoints: GET /api/wishlist, POST /api/wishlist, DELETE /api/wishlist/{wishlist}
- Admin API:
  - Admin-only resource routes registered under `/api/admin/*` in [routes/api.php](routes/api.php)

Refer to the route files for the full route list: [routes/web.php](routes/web.php), [routes/api.php](routes/api.php).

## File Structure (high level)

- app/ — PHP application code (Controllers, Models, Providers)
  - app/Http/Controllers — web & API controllers
  - app/Models — Eloquent models
  - app/Providers — service providers (e.g. [`App\Providers\RouteServiceProvider`](app/Providers/RouteServiceProvider.php))
- config/ — application configuration ([config/app.php](config/app.php))
- resources/views — Blade templates for web UI
- routes/ — route definitions ([routes/web.php](routes/web.php), [routes/api.php](routes/api.php))
- public/ — compiled assets (can be ignored in documentation; front-end tooling is configured via [package.json](package.json))
- tests/ — unit & feature tests (configured by [phpunit.xml](phpunit.xml))

## Running the project (local)

1. Copy .env example and set up database:
   - cp .env.example .env
   - set database credentials and APP_URL in [.env](.env)
2. Install PHP deps:
   - composer install
3. Install node deps & compile assets:
   - npm install
   - npm run dev (or npm run build)
4. Run migrations & seeders:
   - php artisan migrate --seed
5. Run app:
   - php artisan serve
6. Run tests:
   - vendor/bin/phpunit (configured by [phpunit.xml](phpunit.xml))

## Notes for Contributors

- Focus on controllers, API resources and tests when adding features.
- Keep route groups organized by middleware and role, as in [routes/web.php](routes/web.php) and [routes/api.php](routes/api.php).
- Place new controllers under the appropriate namespace and folder (e.g. `app/Http/Controllers/Admin`).
- Add/update API resource tests in `tests/` and register any new config in `config/` as needed.

## Where to look first in the repo

- Route definitions: [routes/web.php](routes/web.php), [routes/api.php](routes/api.php)  
- Dashboard / role routing: [`App\Http\Controllers\DashboardController`](app/Http/Controllers/DashboardController.php)  
- Models: `app/Models/*` (see links above)  
- Views: `resources/views`  

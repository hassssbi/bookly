# 📖 Bookly

Bookly is a Laravel web application for buying and selling books.
It supports three roles — **Admin**, **Seller**, and **Customer** — and combines a traditional Laravel MVC web app with a small JSON API protected by Sanctum.

## 🔍 High-level Overview

### Roles

* **Admin**

  * Manage **users**
  * Review & approve/reject **seller applications**
  * Manage **categories**
  * See a high-level **analytics dashboard** (users, sellers, orders, revenue, top categories/sellers)

* **Seller**

  * Apply to become a seller from a normal customer account
  * Maintain a **seller profile** (store name, contact, address)
  * CRUD their **books**, including cover image upload
  * See a **seller dashboard** (own books, revenue, units sold, top books)
  * View **orders that contain their books** (seller-side order view)

* **Customer**

  * **Register & log in**
  * **Browse** the shop with filters (search, category, price)
  * View a **book hero page** (cover, description, seller, category)
  * Add books to **wishlist** (with “already in wishlist” state)
  * Go through a **checkout + mock payment** flow
  * View their **order history** and individual order details

### Main UX Flow

* After login:

  * **Admin** → `/dashboard` (admin dashboard)
  * **Seller** → `/dashboard` (seller dashboard)
  * **Customer** → `/shop` (storefront; customers don’t need a stats dashboard first)

---

## 🧱 Tech Stack & Architecture

* **Framework**: Laravel (classic MVC structure)
* **Auth**: Laravel auth + **Sanctum** for API authentication
* **DB**: Eloquent ORM, with UUID primary key for `users.id`
* **Views**: Blade templates, based on the AdminLTE theme
* **Front-end**: Mostly server-rendered Blade; a bit of vanilla JS for UX (modals, counters, charts)
* **Charts**: Chart.js for simple dashboard graphs

### Key Patterns

* **Models** (`app/Models`):

  * `User` (with `role` column: `admin`, `seller`, `customer`)
  * `SellerProfile` (per-seller info + `status`: `pending`, `approved`, `rejected`)
  * `Category`
  * `Book` (belongs to `Category` & `User` as seller, uses slug for routing)
  * `Order`, `OrderItem`
  * `Wishlist`
* **Controllers** (`app/Http/Controllers`):

  * `DashboardController` — decides which dashboard/redirect to show based on the logged-in user’s role
  * `Admin\*` — user, seller, category management
  * `Seller\*` — seller profile, books, seller-side orders
  * `Shop\*` — public shop + book hero page
  * `Customer\*` — checkout, payment, customer orders, wishlist
  * `Api\*` — JSON endpoints for books, categories, orders, wishlist, seller profile (protected with Sanctum)
* **Routes**:

  * `routes/web.php` — all browser pages and HTML forms

    * Role-based groups with `auth` + `role:admin`, `role:seller`, `role:customer`
    * Clean, slug-based book/category URLs using route model binding:

      * `/shop`
      * `/shop/books/{book:slug}`
      * `/shop/categories/{category:slug}`
  * `routes/api.php`

    * Public API endpoints for listing books/categories
    * Protected API endpoints for logged-in users via `auth:sanctum`

---

## 🔐 Authentication & Roles

* **Authentication**

  * Normal web login/register for all users
  * Sanctum used for API auth with `auth:sanctum` middleware

* **Role handling**

  * `users.role` column stores a simple string (`admin`, `seller`, `customer`)
  * Custom `role:` middleware applied to route groups:

    * `role:admin`
    * `role:seller`
    * `role:customer`
    * or combinations like `role:admin,seller`

* **Seller application flow**

  * All new registrations start as **customers**
  * Customers can navigate to `Apply as Seller`

    * Creates/updates a `SellerProfile` with `status = pending`
  * Admin reviews applications under the admin seller management page

    * On approval:

      * `seller_profiles.status` → `approved`
      * `users.role` → `seller`
    * On rejection:

      * `seller_profiles.status` → `rejected`
      * `rejection_reason` stored for display to the user

---

## 🛒 Shop, Orders & Payments

### Browsing

* `/shop` — searchable, paginated grid of books:

  * Filters: search by title, category dropdown, min/max price
  * Each book card shows:

    * Cover image (or placeholder)
    * Title
    * Category
    * Price
    * Stock status
    * “View details” button
    * “Add to wishlist” (if logged in as customer; disabled if already in wishlist)

* `/shop/books/{book:slug}` — book hero page:

  * Larger cover image
  * Category + seller name
  * Price & stock
  * Description
  * “Add to wishlist”
  * “Buy now” with:

    * Quantity input
    * Confirmation modal summarising quantity and total
    * Redirect into checkout flow

### Checkout & Mock Payment

**Flow:**

1. Customer clicks **Buy now** on a book hero page → opens a confirmation modal.
2. On confirmation:

   * POST to `customer/checkout/start`
   * Book and quantity stored in session
3. `/customer/checkout`:

   * Shows summary (title, qty, unit price, total)
   * Customer chooses **payment method**:

     * FPX
     * Card
     * E-wallet
4. On confirmation:

   * Creates an `Order` with `status = pending_payment`
   * Creates corresponding `OrderItem`
   * Decrements stock
   * Redirects to a **mock payment page**
5. Mock gateway (`/customer/payment/{order}`):

   * Shows order details & payment method
   * “Pay Now” → sets `status = paid`, sets `paid_at`, redirects to order detail
   * “Cancel Payment” → sets `status = cancelled`, restores stock, redirects to order list

### Customer Orders & Wishlist

* **Orders**

  * `/customer/orders` — list of customer’s orders with status and total
  * `/customer/orders/{id}` — shows items, quantities, prices, and totals

* **Wishlist**

  * `/customer/wishlist` — grid of saved books
  * “Add to wishlist” on shop/hero pages:

    * Uses `Wishlist::firstOrCreate` to avoid duplicates
    * If a book is already in wishlist:

      * Button is disabled and labelled “In Wishlist”
  * Remove from wishlist via a simple DELETE form

---

## 🏪 Seller Features

* **Seller dashboard** (`/dashboard` when role = seller)

  * Summary cards:

    * Total books
    * Active books
    * Units sold (for paid orders)
    * Total revenue (for paid orders)
  * Chart:

    * Revenue over the last 7 days (Chart.js line chart)
  * Table:

    * Top selling books (units + revenue)
    * Recent orders that include this seller’s books

* **Books management**

  * List, create, edit, delete books
  * Cover image upload stored in `storage/app/public`, served via `storage` symlink
  * Slug automatically generated for SEO-friendly URLs

* **Seller-side orders**

  * `/seller/orders` — list of order items that involve the seller’s books
  * `/seller/orders/{order}` — detail view of a specific order, but restricted to:

    * Only line items belonging to this seller
    * Seller’s own total for that order

---

## 🛠 Admin Features

* **Admin dashboard** (`/dashboard` when role = admin)

  * Counters:

    * Total users, customers, sellers
    * Pending seller applications
    * Total books, active books, out-of-stock books
    * Paid orders & total revenue
  * Charts & tables:

    * Orders & revenue over the last 7 days (Chart.js bar + line combo)
    * Top categories by revenue
    * Top sellers by revenue

* **Management panels**

  * Users

    * CRUD for users
    * Role management
  * Categories

    * CRUD with sorting/filtering
  * Sellers

    * List all sellers
    * Review seller applications
    * Approve/reject with optional rejection reason

---

## 🎨 UI Details

* Layout built on **AdminLTE** theme
* Shared layout includes:

  * Sidebar (role-aware menu: admin, seller, customer sections)
  * Top navbar
  * Breadcrumbs
  * Flash messages (e.g. “Book added to wishlist”, “Application submitted”)
* Dashboard numbers use a small JS helper to **animate counters** from 0 up to their values on page load.

---

## 🚀 Getting Started (Local)

1. **Clone & install dependencies**

   ```bash
   git clone https://github.com/hassssbi/bookly.git
   cd bookly
   composer install
   npm install
   ```

2. **Environment**

   ```bash
   cp .env.example .env
   ```

   * Set your DB connection, `APP_URL`, and other relevant settings inside `.env`.

3. **App key**

   ```bash
   php artisan key:generate
   ```

4. **Database**

   ```bash
   php artisan migrate
   # Optional: if you have seeders
   # php artisan db:seed
   ```

5. **Storage symlink (for covers)**

   ```bash
   php artisan storage:link
   ```

6. **Run the app**

   ```bash
   php artisan serve
   ```

7. **Frontend assets** (if needed)

   ```bash
   npm run dev   # for local dev
   # or
   npm run build # for production build
   ```

8. **Access**

   * Visit `http://localhost:8000`
   * Register a user (customer by default)
   * Log in as admin (or promote your user via DB) to access admin dashboard
   * Apply as seller from the customer account to test the seller flow

---

## 🧭 Where to Look First

* **Routes**

  * Web: `routes/web.php`
  * API: `routes/api.php`
* **Role routing & dashboards**

  * `app/Http/Controllers/DashboardController.php`
* **Core models**

  * `app/Models/User.php`, `Book.php`, `Category.php`, `Order.php`, `OrderItem.php`, `SellerProfile.php`, `Wishlist.php`
* **Role-specific controllers**

  * `app/Http/Controllers/Admin/*`
  * `app/Http/Controllers/Seller/*`
  * `app/Http/Controllers/Customer/*`
  * `app/Http/Controllers/Shop/*`
  * `app/Http/Controllers/Api/*`
* **Views**

  * `resources/views/layouts/*`
  * `resources/views/dashboard/*`
  * `resources/views/shop/*`
  * `resources/views/admin/*`
  * `resources/views/seller/*`
  * `resources/views/customer/*`

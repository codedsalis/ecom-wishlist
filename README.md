# E‑Commerce Wishlist API
 
A RESTful API that provides authentication, product management, and wishlist functionality. This project demonstrates clean architecture using Controllers, Services, DTOs, API Resources, Sanctum authentication, and Pest testing.
 
---
 
## 🚀 Features
 
* User registration & authentication (Laravel Sanctum)
* Product listing with pagination & search
* Single product retrieval
* Wishlist management (add/remove products)
* Paginated wishlist products
* Clean API response structure
* OpenAPI documentation via Scramble
* Feature tests written with Pest
 
---
 
## 🛠 Tech Stack
 
* **Framework:** Laravel 12
* **PHP:** >= 8.2
* **Authentication:** Laravel Sanctum
* **Database:** MySQL / SQLite (for testing)
* **Testing:** Pest PHP
* **API Documentation:** Dedoc Scramble
 
---
 
## ✅ Requirements
 
Ensure the following are installed on your machine before running the project:
 
* PHP **8.2 or higher**
* Composer
* MySQL 8+ or SQLite
* Git
 
---
 
## ▶️ Getting Started
 
Follow these steps to get the API running locally.
 
### 1. Clone the repository
 
```bash
git clone <repository-url>
cd ecom-wishlist
```
 
### 2. Install PHP dependencies
 
```bash
composer install
```
 
### 3. Environment setup
 
```bash
cp .env.example .env
php artisan key:generate
```
 
Update your database credentials in `.env`.
 
### 4. Run database migrations
 
```bash
php artisan migrate
```
 
Seed the database for test products data:
 
```bash
php artisan db:seed
```
 
### 5. Run tests (optional)
 
```bash
php artisan test
```
 
### 6. Run the application
 
```bash
php artisan serve
```
 
---
 
 # API Documentation
 This section contains a minimal documentation of the available endpoints.
 For a comprehensive overview of the documentation, kindly go to http://127.0.0.1:8000/docs/api

## 🔐 Authentication Endpoints
 
### Register
 
`POST /api/v1/auth/register`
 
**Request Body**
 
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```
 
### Login
 
`POST /api/v1/auth/login`
 
**Response**
 
```json
{
  "status": "success",
  "data": {
    "user": { ... },
    "token": "<sanctum-token>"
  }
}
```
 
---
 
## Product Endpoints
 
> All product endpoints require authentication using bearer token
 
### Fetch Products
 
`GET /api/v1/product`
 
Query Params:
 
* `per_page` (optional)
* `search` (optional)
 
### Fetch Single Product
 
`GET /api/v1/product/{product}`
 
---
 
## Wishlist Endpoints
 
> All wishlist endpoints require authentication using bearer token
 
### Fetch Wishlist Products
 
`GET /api/v1/wishlist`
 
Query Params:
 
* `per_page`
* `search`
 
### Add Product to Wishlist
 
`POST /api/v1/wishlist/{product}`
 
### Remove Product from Wishlist
 
`DELETE /api/v1/wishlist/{product}`
 
---
 
## 🧪 Testing
 
This project uses **Pest** for testing.
 
Run tests with:
 
```bash
php artisan test
```
 
Covered tests include:
 
* Authentication (register & login)
* Product listing & search
* Wishlist add/remove
* Pagination behavior
* Authorization checks
 
---
 
## 📄 API Documentation
 
API documentation is generated using **Scramble**.
 
After running the app:
 
```
Go to http://127.0.0.1:8000/docs/api
```
 
## ✅ Best Practices Used
 
* Service layer abstraction
* DTOs for request data handling
* API Resources for response shaping
* Prices are stored as integers for ease and accuracy when dealing with currency conversion
* Named routes
* Pagination on relationships
* Sanctum token invalidation on login
* Services are documented with doc block anotations
* And others
 
---
 
## 📌 Notes
 
* Password hashing is handled via Laravel’s `hashed` cast
* Wishlist uses a proper many-to-many relationship with products
* Tests run against SQLite for speed and isolation
* Unit tests are not covered because of time constraint, only feature tests are covered.
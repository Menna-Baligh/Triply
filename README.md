# 🗺️ Triply

## 📖 Project Description
The **Travel & Tour API** is a **RESTful backend application** built with **Laravel**.
It provides **secure endpoints** for managing travels and tours, including:

* **Admin-only operations**: manage users, travels, and tours.
* **Public endpoints**: browse and filter available travels and tours.

This project showcases **clean API design**, **role-based access control**, and **Laravel best practices**.

---

## 🎯 Goals
At the end, the project provides:

- 🔒 **Private (Admin) Endpoints**
  - Create new users (can also be done via an artisan command).
  - Create new travels.
  - Create new tours for a travel.

- ✏️ **Private (Editor) Endpoints**
  - Update an existing travel.

- 🌍 **Public (No Auth) Endpoints**
  - Get a paginated list of **public travels**.
  - Get a paginated list of **tours** by the travel slug.
    - Supports filters: `priceFrom`, `priceTo`, `dateFrom`, `dateTo`.
    - Supports sorting: by `price` (asc/desc) and always sorted by `startingDate` asc.

---

## 🛠️ Tech Stack
- **Laravel** (Backend Framework)
- **MySQL** (Database)
- **Laravel Sanctum** (Authentication)
- **Scalar** (Beautiful API documentation UI)
- **Scribe** (API documentation generator)
- **Laravel Pint** (Code style & formatting)

---

## 🚀 Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/Menna-Baligh/Triply.git
   cd Triply
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database credentials.

4. **Run migrations & seeders**
   ```bash
   php artisan migrate --seed
   ```

5. **Run the server**
   ```bash
   php artisan serve
   ```

---

## 📌 API Documentation
- **Scalar UI**: `http://127.0.0.1:8000/scalar`
*(View the API docs in browser)*

---

## 🧪 Testing
Run the **Feature Tests** to ensure your APIs work as expected:
```bash
php artisan test
```

---

## 👤 Roles & Permissions
- **Admin**: Full control (manage users, travels, tours).
- **Editor**: Can update travels.
- **Public**: Browse travels and tours.

---



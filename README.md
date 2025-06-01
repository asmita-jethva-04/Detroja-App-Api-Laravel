
# 🔐 Detroja App API

A RESTful Laravel API designed to manage and serve user-related data with secure token-based authentication.  
Handles multiple modules including users, admins, categories, relations, villages, directories, history, and more.

---

## 📦 Features

- User & Admin Management
- Secure Authentication with Token
- Search & Directory APIs
- Category & Village Data
- History Tracking & Relations
- Role-Based Access
- Built with Laravel Framework

---

## 🔐 Authentication

All endpoints require a **Bearer Token** to access protected routes.  
Include the token in the `Authorization` header as follows:

```

Authorization: Bearer {your\_token}

```

---

## 📁 API Modules

- `/api/auth` – Login, Register, Token Handling
- `/api/user` – User Profile, Update, Listing
- `/api/admin` – Admin Controls
- `/api/category` – Category CRUD
- `/api/village` – Village Information
- `/api/relation` – User Relations
- `/api/history` – View User History
- `/api/search` – Directory & Data Search

---

## 🛠️ Setup Instructions

1. Clone the repository  
2. Run `composer install`  
3. Configure `.env` for database & app key  
4. Run `php artisan migrate`  
5. Use Postman or any API client to test

---

## 📄 License

This project is open-source and free to use under the MIT License.


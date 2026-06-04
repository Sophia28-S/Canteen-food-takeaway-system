# 🍽️ Canteen Food Take-Away System

A full-stack web application built for **Kristu Jayanti College** to digitalise and streamline the campus canteen ordering experience — eliminating queues, manual billing, and order miscommunication.

---

## 📌 Overview

The Canteen Takeaway System replaces the traditional counter-based canteen workflow with a digital platform where students can browse the live menu, manage a cart, place and schedule orders, select payment methods, and track their orders in real time — all from a browser.

Built using **PHP, MySQL, and vanilla JavaScript** with a fully custom CSS design system centred on an orange (`#f97316`) palette.

---

## ✨ Features

### 👨‍🎓 Student Module
- Register and login restricted to `@kristujayanti.com` email addresses
- Browse live digital menu with stock visibility and category filters
- Add items to cart, adjust quantities
- Place immediate or scheduled pickup orders
- Choose between cash and online (QR demo) payment
- Track orders through a 5-stage pipeline in real time
- View full order history with colour-coded status badges
- Manage profile and update password

### 👨‍🍳 Staff Module
- Real-time kitchen dashboard with pending order counts and revenue
- Add, edit, and manage menu items with image uploads
- Manage stock and availability
- Accept and progress orders through the pipeline:
  `Pending → Accepted → Preparing → Ready → Completed`
- Automated time-based status transitions (5 min → Preparing, 10 min → Ready)
- View today's full order log and pickup queue

### 🛠️ Admin Module
- Create and manage user accounts across all roles
- Manage food categories and menu structure
- Configure pickup slots and time windows
- System-wide settings: ordering toggle, payment method toggles, tax %, service charge, auto-cancel timer, max items per order, stock alerts
- View all orders with revenue and payment breakdowns
- Sales reports: top-selling items, daily volumes, revenue trends

---

## 🗄️ Database Schema

Database: `canteen_db` — 8 relational tables

| Table | Description |
|---|---|
| `users` | Stores all user accounts with hashed passwords and roles |
| `categories` | Food category definitions |
| `menu_items` | Menu catalogue with pricing, stock, and images |
| `orders` | Order records with status, payment, and slot linkage |
| `order_items` | Individual line items per order |
| `pickup_slots` | Configurable pickup time windows |
| `time_slots` | Structured slot windows with capacity |
| `system_settings` | Singleton table for global operational flags |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.x |
| Database | MySQL 8.0 |
| Frontend | HTML, CSS, Vanilla JavaScript |
| Server | Apache (XAMPP) |
| DB Management | phpMyAdmin |
| Version Control | Git |

No external JS frameworks or CSS libraries — fully custom.

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- A modern browser (Chrome, Firefox, Edge)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/sophiasai635-alt/Canteen-food-takeaway-system.git
   ```

2. **Move to XAMPP's web root**
   ```bash
   # Copy the project folder to:
   # Windows: C:/xampp/htdocs/canteen-takeaway-system
   # Linux/macOS: /opt/lampp/htdocs/canteen-takeaway-system
   ```

3. **Import the database**
   - Start Apache and MySQL from the XAMPP Control Panel
   - Open `http://localhost/phpmyadmin`
   - Create a new database named `canteen_db`
   - Import the provided `canteen_db.doc` file, paste in the SQl and then click submit

4. **Configure the database connection**
   - Open `api/db.php`
   - Update credentials if needed:
     ```php
     $host = "localhost";
     $db   = "canteen_db";
     $user = "root";
     $pass = "";
     ```

5. **Run the application**
   ```
   http://localhost/canteen-takeaway-system/
   ```

---

## 👤 Default Roles

| Role | Access |
|---|---|
| Student | Menu, cart, orders, profile |
| Staff | Kitchen dashboard, menu management, order pipeline |
| Admin | Full system control, reports, settings |

> Registration is open to `@kristujayanti.com` emails only. Admin and staff accounts can be created directly from the Admin panel.

---

## 📁 Project Structure

```
canteen-takeaway-system/
├── admin/          # Admin module pages
├── staff/          # Staff module pages
├── students/       # Student module pages
├── api/
│   ├── db.php          # Database connection
│   └── session.php     # Session & role-based access control
├── auth/           # Login, registration, logout
├── assets/
│   ├── css/        # Stylesheets (main.css + module CSS)
│   └── js/         # Vanilla JS files
├── uploads/        # Uploaded menu item images
└── canteen_db.sql  # Database dump
```

---

## 🧪 Testing

14 test cases were executed across all three modules covering registration, login, order placement, cart validation, staff order management, admin configuration, and access control. 10/14 passed; known failures in automated status transitions and real-time settings propagation are noted for future resolution.

---

## 🔮 Future Enhancements

- Live payment gateway integration (Razorpay / PayU)
- Push notifications and SMS alerts (Firebase / Twilio)
- Native Android/iOS app (Flutter / React Native)
- Cron-based automatic order management
- Advanced analytics dashboard with visual charts
- Multi-canteen support
- Loyalty and reward system
- Nutritional and dietary information on menu items
- Inventory and supplier management module

---

## 👩‍💻 Author

Sophia S.
---

## 📄 License

This project was developed as an academic submission. All rights reserved by the author.

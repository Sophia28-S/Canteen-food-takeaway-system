# Canteen Food Take-Away System

[cite_start]The Canteen Food Takeaway System is a full-stack web application developed for Kristu Jayanti College to digitalise and streamline the campus canteen ordering experience[cite: 4]. [cite_start]The application eliminates the inefficiencies of traditional manual counter-based canteen operations, such as long queues and verbal order miscommunication, by introducing real-time order tracking and sales visibility[cite: 5].

## 🚀 Tech Stack

* [cite_start]**Front-End:** HTML, custom CSS (centred on an orange `#f97316` design system), and vanilla JavaScript[cite: 5, 41].
* [cite_start]**Back-End:** PHP 8.x[cite: 670].
* [cite_start]**Database:** MySQL 8.0.x[cite: 670].
* [cite_start]**Server Environment:** Apache HTTP Server (e.g., XAMPP)[cite: 188, 670].

## ✨ Key Features by Role

[cite_start]The system serves three distinct user roles, each with a dedicated dashboard and automatic role-based redirection upon login[cite: 6].

### 🎓 Student Module
* [cite_start]**Exclusive Access:** Registration and login are restricted exclusively to verified `@kristujayanti.com` email addresses[cite: 7].
* [cite_start]**Digital Menu & Cart:** Browse the live digital menu with real-time stock visibility and manage cart items[cite: 8, 65].
* [cite_start]**Flexible Ordering:** Place immediate orders or schedule pickup slots for later[cite: 8].
* [cite_start]**Payment Options:** Choose between cash or online payment workflows[cite: 8].
* [cite_start]**Live Tracking:** Track orders in real-time through a 5-stage status pipeline[cite: 8].

### 👨‍🍳 Staff Module
* [cite_start]**Menu & Inventory Management:** Add, edit, and manage menu items, categories, and stock levels seamlessly[cite: 9].
* [cite_start]**Order Processing Pipeline:** Manage active orders through a structured workflow: `Pending` → `Accepted` → `Preparing` → `Ready` → `Completed`[cite: 9].
* [cite_start]**Automated Transitions:** Time-based automated status transitions help reduce manual intervention during peak hours[cite: 9].
* [cite_start]**Queue Management:** An organized pickup queue view to manage counter handovers efficiently[cite: 233].

### ⚙️ Admin Module
* [cite_start]**System Control:** Maintain full system control including user account management and food category administration[cite: 10].
* [cite_start]**Scheduling:** Configure dynamic pickup slots and time windows to manage student footfall[cite: 10, 67].
* [cite_start]**Global Settings:** Real-time toggling for ordering availability, payment methods, tax percentages, and auto-cancellation timers[cite: 67, 161].
* [cite_start]**Analytics & Reporting:** Access sales reports, order history, and top-item analytics for informed decision-making[cite: 10, 67].

## 🗄️ Database Architecture

[cite_start]The application is powered by a well-structured `canteen_db` MySQL database[cite: 11, 42]. The relational schema ensures data integrity and scalability across eight core tables:

* [cite_start]`users`: Stores account credentials and roles[cite: 11, 42].
* [cite_start]`categories`: Organises food offerings[cite: 11, 42].
* [cite_start]`menu_items`: Stores item details, stock, and availability[cite: 11, 42].
* [cite_start]`orders` & `order_items`: Manages the full lifecycle and details of placed orders[cite: 11, 42].
* [cite_start]`pickup_slots` & `time_slots`: Handles the time-window based scheduling system[cite: 11, 43].
* [cite_start]`system_settings`: A singleton table governing global application behavior[cite: 11, 43, 333].

## 🛠️ Setup & Installation

1. Clone the repository to your local machine.
2. [cite_start]Install a local server environment like **XAMPP**[cite: 670].
3. Move the project folder into your XAMPP `htdocs` directory.
4. [cite_start]Start the **Apache** and **MySQL** modules from the XAMPP Control Panel[cite: 679].
5. [cite_start]Open phpMyAdmin and create a new database named `canteen_db`[cite: 11, 679].
6. Import the provided `.sql` database file into `canteen_db`.
7. [cite_start]Update the database connection credentials in the backend configuration file (`api/db.php`) if necessary[cite: 726, 727].
8. Access the application via `http://localhost/your-project-folder-name`.

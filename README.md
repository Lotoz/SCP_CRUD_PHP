# 📂 SCP Foundation Secure Database System

![SCP Logo](https://upload.wikimedia.org/wikipedia/commons/e/ec/SCP_Foundation_%28edificio%29_logo.svg)

> **⚠️ WARNING:** This software is an **educational project** developed to demonstrate skills in **Native PHP, OOP, and MVC Architecture**. It is not designed for use in critical production environments.

## 📖 General Description

The **SCP Secure Database System** is a *Full-Stack* web application that simulates the classified database of the SCP Foundation. It allows secure management of anomaly files, personnel administration, containment site assignments, and operational tasks.

The system is built strictly following the **Model-View-Controller (MVC)** pattern without using frameworks, ensuring clean, modular, and scalable code.

---

## 🚀 Main Features

### 🔐 Security and Authentication

* **Login System:** Roles and Security Levels (1 to 5).
* **Protection:** Password hashing (`Bcrypt`), CSRF tokens in forms, and protection against *Session Fixation*.
* **Error Prevention:** Self-deletion blocking (an administrator cannot delete their own account).

### 📂 Anomaly Management (SCPs)

* **Full CRUD:** Create, read, edit, and delete files.
* **Multimedia Management:** Image upload with **automatic renaming** based on the SCP ID.
* **Public Wiki:** Dynamic card visualization with styles according to the object's class (Safe, Euclid, Keter).

### 🛠️ Personnel Administration

* **Full Control:** Exclusive panel for Level 5 (O5 Council).
* **UX:** Password visualization with *toggle* button and real-time validations (JS).

---

## ⚙️ Technologies Used

* **Backend:** PHP 8.2+ (Native, OOP).
* **Database:** MySQL / MariaDB (InnoDB).
* **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (ES6).
* **Server:** Apache (XAMPP/LAMPP).

---

## 📋 System Rules (Lore & Business Logic)

This project implements strict rules to simulate the SCP Foundation's bureaucracy:

1. **SCP Nomenclature:** All anomaly IDs must strictly start with **`SCP-`** (e.g., `SCP-173`).
2. **Containment Protocol:**
    * Classes **SAFE, EUCLID, and NEUTRALIZED** *must* have a Containment Site assigned mandatorily.
    * Classes **KETER and THAUMIEL** are the only ones that can have unknown location (`NULL`).
3. **User Integrity:**
    * Usernames only accept letters, numbers, hyphens, and underscores (Regex: `/^[a-zA-Z0-9_-]+$/`).
    * A Level 0 user (Class-D) has *Read-Only* access to their tasks.

---

## 🔧 Installation and Deployment

Follow these steps to set up the project in your local environment:

### 1. Clone the Repository

```bash
git clone https://github.com/tu-usuario/scp-crud-php.git
cd scp-crud-php
```

### 2. Configure the Local Server

1. Database

    Open your database manager (phpMyAdmin, DBeaver, etc.).

    Create a database named scp_data.

    Import the script located in: 📂 DATABASE/scp_data.sql

2. Configuration

Edit the configuration file with your local credentials: 📂 SCP_CRUD_PHP/config/Database.php

```php
private $host = "localhost";
private $db_name = "scp_data";
private $username = "view"; // Your username
private $password = "";     // Your password
```

Default credentials are view / yX/I!geU1xKbG3F[ for testing purposes.

3. Permissions (Linux/Mac Only)

Ensure the upload folder has write permissions:

```bash
chmod -R 777 views/CRUD/anomalies/assets/img/
```

---

## 📂 Project Structure

```
/
├── DATABASE/           # SQL Scripts and Seeders
├── EXTRA/              # Test Credentials and Notes
├── pictures_README/    # Images for Documentation
├── SCP_CRUD_PHP/       # APPLICATION SOURCE CODE
│   ├── config/         # DB Connection and SessionManager
│   ├── controllers/    # Business Logic
│   ├── models/         # Entities
│   ├── repositories/   # SQL Queries (Repository Pattern)
│   ├── views/          # User Interface (HTML/PHP)
│   └── index.php       # Main Router
└── README.md           # This file
```

---

## 🔑 Access Credentials (Demo)

You can find a complete list of test users in the 📂 EXTRA/ folder.

---

## 📜 License

This project is open source and under the MIT license. Check the LICENSE file for more details.

---
Secure. Contain. Protect.

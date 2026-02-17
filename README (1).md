# 🚗 Car Rental Management System

A role-based Car Rental Management System built using PHP, MySQL, Bootstrap, and XAMPP.

This system allows customers to book cars and agencies to manage vehicles and approve bookings through a structured workflow.

---

## 📌 Project Overview

This web application provides:

- Role-based Authentication (Customer / Agency)
- Car Management (Add, Edit, Delete)
- Booking System
- Booking Approval Workflow
- Status Management
- File Upload Handling
- Database Export for Replication

The system follows secure coding practices using prepared statements and transactions.

---

## 🛠 Technologies Used

- PHP (Core PHP)
- MySQL
- XAMPP (Apache + MySQL)
- Bootstrap 5
- HTML5 / CSS3
- JavaScript (Basic Validation)

---

## 📂 Project Structure

car_rental/
│
├── agency/
│   ├── add_car.php
│   ├── edit_car.php
│   ├── delete_car.php
│   ├── my_cars.php
│   ├── bookings.php
│   ├── update_booking.php
│   └── dashboard.php
│
├── customer/
│   └── dashboard.php
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   └── db.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/cars/
│
├── index.php
├── available_cars.php
├── book_car.php
└── car_rental.sql

---

## 🔐 User Roles

### 👤 Customer
- Register / Login
- View Available Cars
- Book Cars
- View Booking Status

### 🏢 Agency
- Register / Login
- Add Car
- Edit Car
- Delete Car
- View Bookings
- Approve / Reject Bookings

---

## 🔄 Booking Workflow

1. Customer selects a car and submits booking request.
2. Booking status is set to **Pending**.
3. Agency can:
   - Approve → Status becomes **Confirmed**
   - Reject → Status becomes **Cancelled**
4. Car status updates accordingly.

---

## 🔒 Security Features

- Prepared Statements (Prevents SQL Injection)
- Role-based Access Control
- Session Management
- URL Tampering Prevention
- Input Validation
- Transaction Handling for Booking Logic

---

## 🗄 Database Setup Instructions

1. Start XAMPP.
2. Open phpMyAdmin:
   http://localhost/phpmyadmin
3. Create a new database:
   car_rental
4. Click Import
5. Upload the file:
   car_rental.sql
6. Click Go

Database is now ready.

---

## 🚀 How to Run the Project

1. Install XAMPP.
2. Copy the project folder to:
   C:\xampp\htdocs\
3. Start:
   - Apache
   - MySQL
4. Open browser:
   http://localhost/car_rental

---

## 🧪 Testing Checklist

The following features were tested:

- User Registration (Customer & Agency)
- Login Authentication
- Add/Edit/Delete Car
- Booking Creation
- Date Validation
- Booking Approval / Rejection
- Status Updates
- Image Upload
- Double Booking Prevention
- Role-based Access Protection
- SQL Export Portability

---

## 📦 Submission Contents

The submission includes:

- Complete project folder
- car_rental.sql database export file
- All required PHP source files
- Assets and uploads folder

---

## 📈 Future Improvements We Can make

- Payment Gateway Integration
- Overlapping Date Validation
- Admin Panel
- Revenue Tracking Dashboard
- Booking History Analytics
- Email Notifications

---

## 👨‍💻 Developer Notes

This project was built following structured folder architecture, modular file separation, and secure backend logic.

Prepared statements were used to prevent SQL injection.
Transactions were used during booking to ensure data consistency.

---

## 📜 License

This project is developed for academic purposes.

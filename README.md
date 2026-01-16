# Electric Bike Loan Management System

A PHP-based web application designed to manage the process of applying for, approving, and repaying loans for electric bikes. This system connects student borrowers with administrators to facilitate easy access to e-mobility.

## Table of Contents
1. [Project Overview](#project-overview)
2. [Features](#features)
3. [System Architecture](#system-architecture)
4. [Installation & Setup](#installation--setup)
5. [Usage Guide](#usage-guide)
6. [Folder Structure](#folder-structure)
7. [Documentation](#documentation)

## Project Overview

The **Electric Bike Loan Management System** allows students to apply for loans to purchase electric bikes. It features a complete workflow from user registration and document upload to admin approval and automated repayment scheduling. The system integrates with **Paystack** for secure online repayments.

## Features

### For Borrowers (Students)
- **User Registration & Login:** Secure account creation.
- **Loan Application:** Browse bike models, select loan duration (3, 6, 12 months), and submit applications.
- **Document Upload:** Upload ID cards and utility bills for verification.
- **Dashboard:** Track loan status (Pending, Approved, Rejected).
- **Repayment Management:** View repayment schedules and history.
- **Online Payment:** Pay monthly installments securely via Paystack.

### For Administrators
- **Admin Dashboard:** Overview of system activities.
- **Loan Management:** View, approve, or reject loan applications.
- **Document Verification:** Review uploaded user documents.
- **Repayment Monitoring:** Track all payments and overdue schedules.

## System Architecture

The project follows a procedural PHP architecture with a clear separation of concerns:
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla).
- **Backend:** Core PHP (No frameworks).
- **Database:** MySQL.
- **Payment Gateway:** Paystack API.

### Documentation
Detailed system documentation can be found in the `docs/` folder:
- [User Requirements](docs/user_requirements.md)
- [Non-Functional Requirements](docs/non_functional_requirements.md)
- [Use Case Diagram](docs/use_case_diagram.md)
- [Use Case Descriptions](docs/use_case_descriptions.md)
- [Behavioral Diagrams (Sequence)](docs/behavioral_diagrams.md)
- [Structural Diagrams (Class)](docs/structural_diagrams.md)
- [Database Design (ERD)](docs/database_design.md)

## Installation & Setup

### Prerequisites
- **XAMPP** (or any LAMP/WAMP stack)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Composer** (optional, if dependencies are added later)

### Steps
1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yourusername/ebike-loan-system.git
   cd ebike-loan-system
   ```

2. **Configure the Database:**
   - Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
   - Create a new database named `ebike_loan_db`.
   - Import the database schema (if provided as `.sql`) or ensure the following tables exist: `users`, `loan_applications`, `repayment_schedule`, `repayments`.
   - Update `includes/db_connect.php` if your credentials differ from the default (`root`, empty password).

3. **Configure Paystack:**
   - Open `pay_installment.php` (and `verify_payment.php` if applicable).
   - Replace the placeholder Paystack Secret Key with your test keys.

4. **Run the Application:**
   - Move the project folder to your server's root directory (e.g., `C:/xampp/htdocs/`).
   - Open your browser and navigate to `http://localhost/ebike-loan-system/`.

## Usage Guide

### 1. Register & Login
- Visit the homepage to register as a new user.
- Log in to access the borrower dashboard.

### 2. Applying for a Loan
- Click "Apply for Loan".
- Select your preferred bike model and loan tenure.
- Upload valid ID and Utility Bill.
- Submit for approval.

### 3. Admin Approval
- Log in as an admin (navigate to `/admin/login.php` or manually set a user role to 'admin' in the database).
- Go to "View Loans" to see pending applications.
- Click "Approve" to sanction the loan and generate the repayment schedule.

### 4. Making Repayments
- As a borrower with an approved loan, go to "Repayment Schedule".
- Click "Pay Now" on the next due installment.
- Complete the payment via Paystack.

## Folder Structure

```
/
├── admin/                  # Admin-specific pages (approve loans, reports)
├── assets/                 # CSS, Images, JS
├── docs/                   # System documentation (Architecture, Diagrams)
├── includes/               # Reusable components (DB connect, headers)
├── uploads/                # User uploaded documents
├── apply_loan.php          # Loan application logic
├── dashboard.php           # Borrower dashboard
├── pay_installment.php     # Payment processing
├── register.php            # User registration
├── repayment_schedule.php  # View installments
└── ...
```

## License
This project is open-source and available for educational purposes.

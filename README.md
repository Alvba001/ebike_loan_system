# Electric Bike Loan Management System

## Project Overview
The Electric Bike Loan Management System is a web-based application designed to automate the process of applying for, approving, and managing loans for electric bikes. It serves two main user roles: **Borrowers** (who apply for loans) and **Administrators** (who review and approve them).

## Features
*   **User Registration & Authentication**: Secure login for Borrowers and Admins.
*   **Loan Application**: Step-by-step form including file uploads (ID Card, Utility Bill) and Guarantor details.
*   **Admin Dashboard**: Overview of all loans, with capabilities to Approve or Reject applications.
*   **Automated Calculations**: Automatic calculation of interest and generation of monthly repayment schedules upon approval.
*   **Repayment Tracking**: Borrowers can view their schedule and make payments (simulated). Admins can track repayment history.

## Technology Stack
*   **Backend**: PHP (Core)
*   **Database**: MySQL
*   **Frontend**: HTML, CSS, JavaScript
*   **Environment**: XAMPP (Apache Server)

## Setup Instructions

### 1. Prerequisites
*   Install **XAMPP** or any local PHP/MySQL server.
*   Ensure PHP 8.0+ is enabled.

### 2. Database Configuration
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named `ebike_loan_db`.
3.  Import the database schema (if provided as `.sql`) or ensure the code's `db_connect.php` matches your local credentials.
    *   File: `includes/db_connect.php`
    *   Default User: `root`
    *   Default Pass: `""` (Empty)

### 3. Application Setup
1.  Copy the project folder to your `htdocs` directory (e.g., `C:\xampp\htdocs\ebike_loan`).
2.  Open your browser and navigate to `http://localhost/ebike_loan`.

### 4. Default Credentials
*   **Admin**: (You may need to manually insert an admin into the `users` table with `role='admin'` or register a user and update the role in the database).
    *   Example SQL: `UPDATE users SET role='admin' WHERE email='admin@example.com';`

## Documentation
Full documentation is available in the `docs/` directory:
*   [User Requirements](docs/user_requirements.md)
*   [System Design & Diagrams](docs/use_case_diagram.md)
*   [Methodology (Chapter 3)](docs/chapter_3.md)
*   [Implementation (Chapter 4)](docs/chapter_4.md)

## License
This project is for educational purposes (Final Year Project).

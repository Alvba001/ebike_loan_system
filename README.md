# Electric Bike Loan Management System

## Project Overview
The Electric Bike Loan Management System is a web-based application designed to facilitate the process of applying for, approving, and managing loans for electric bikes. It serves two main user roles: **Borrowers** (Students/Users) who can apply for loans and track their status, and **Admins** who manage the loan lifecycle.

## Features
- **User Authentication:** Registration and Login for borrowers. Admin login.
- **Loan Application:** Multi-step application process including:
  - Bike selection and loan configuration (Amount, Duration).
  - Personal details (NIN, BVN).
  - Document uploads (ID Card, Utility Bill).
  - Guarantor information collection.
- **Admin Dashboard:** Overview of system stats.
- **Loan Management:** Admin can view, approve, or reject applications.
- **Repayment System:**
  - Automated repayment schedule generation upon approval.
  - Tracking of paid and pending installments.

## Technology Stack
- **Language:** PHP (Core/Procedural)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Environment:** XAMPP (Apache, MySQL)

## Setup Instructions

### Prerequisites
- XAMPP or any LAMP/WAMP stack installed.
- A web browser.

### Installation
1. **Clone/Download:**
   - Place the project folder in your web server's root directory (e.g., `htdocs` in XAMPP).

2. **Database Setup:**
   - Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
   - Create a new database named `ebike_loan_db`.
   - Import the database schema. Since there is no single SQL file, you may need to rely on the application to create tables or check `includes/db_connect.php` for configuration.
   - *Note:* The system includes scripts like `check_schema.php`, `add_guarantor_table.php`, etc., which suggest the schema evolves. Ensure the following tables exist: `users`, `loan_applications`, `guarantor_information`, `repayment_schedule`.

3. **Configuration:**
   - Open `includes/db_connect.php`.
   - Verify the credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "ebike_loan_db";
     ```

4. **Running the App:**
   - Open your browser and navigate to `http://localhost/your_project_folder/`.
   - The entry point is `index.php` (if exists) or `login.php`.

## Usage Guide

### For Borrowers
1. **Register:** Go to `register.php` and create an account.
2. **Login:** Use your credentials to log in.
3. **Apply:** Click "Apply for Loan" on the dashboard. Select your bike and fill in the details.
4. **Guarantor:** After submitting personal info, you will be redirected to add Guarantor details.
5. **Wait:** Wait for Admin approval. Check the dashboard for status updates.
6. **Repay:** Once approved, view your schedule in "Repayment Schedule".

### For Admins
1. **Login:** Access the admin panel (ensure your user account has `role='admin'` in the database).
2. **Review:** Check "View Loans" to see pending applications.
3. **Action:** Click on a loan to view details/documents, then Approve or Reject.
4. **Track:** Monitor repayments.

## Documentation
Detailed system architecture and requirements documentation can be found in the `docs/` directory.

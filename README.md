# Electric Bike Loan Management System

## Overview
The Electric Bike Loan Management System is a web-based application designed to facilitate the process of applying for, approving, and managing loans for electric bikes. It connects borrowers (e.g., students) with administrators who manage the inventory and loan lifecycle.

## Features
### For Borrowers (Users)
*   **Registration & Login**: Secure account creation and access.
*   **Loan Application**: Apply for a bike loan by selecting a model, duration, and uploading required documents (ID, Utility Bill, Guarantor Form).
*   **Dashboard**: View current loan status and upcoming repayments.
*   **Repayment**: Make payments towards the loan and view history.
*   **Notifications**: Receive alerts for loan updates and reminders.

### For Administrators
*   **Dashboard**: Overview of system activity.
*   **Loan Management**: View, approve, or reject loan applications.
*   **Inventory Management**: Add new bikes and assign them to approved loans.
*   **Repayment Monitoring**: Track all payments and generate reminders for due dates.

## Tech Stack
*   **Backend**: PHP (Native)
*   **Database**: MySQL
*   **Frontend**: HTML, CSS, JavaScript
*   **Server**: Apache (via XAMPP/WAMP or similar)

## Setup Instructions

1.  **Prerequisites**
    *   Install XAMPP, WAMP, or any PHP/MySQL environment.

2.  **Database Setup**
    *   Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
    *   Create a new database named `ebike_loan_db`.
    *   Import the provided SQL schema (if available) or create tables as described in `docs/database_design.md`.
    *   *Note: Ensure the database credentials in `includes/db_connect.php` match your local setup.*

3.  **Project Installation**
    *   Clone or download this repository into your server's root directory (e.g., `htdocs/`).
    *   Ensure the `uploads/` directory exists and has write permissions.

4.  **Running the Application**
    *   Start Apache and MySQL services.
    *   Open your browser and navigate to `http://localhost/your-project-folder/login.php`.

## Documentation
Detailed documentation is available in the `docs/` folder:
*   [User Requirements](docs/user_requirements.md)
*   [Non-Functional Requirements](docs/non_functional_requirements.md)
*   [Use Case Diagram](docs/use_case_diagram.md)
*   [Use Case Descriptions](docs/use_case_descriptions.md)
*   [Behavioral Diagrams](docs/behavioral_diagrams.md)
*   [Structural Diagrams](docs/structural_diagrams.md)
*   [Database Design](docs/database_design.md)

## Folder Structure
*   `admin/` - Admin-facing pages (dashboard, loan management).
*   `assets/` - CSS and other static assets.
*   `docs/` - Project documentation.
*   `includes/` - Reusable PHP components (db connection, headers).
*   `uploads/` - Directory for storing user-uploaded documents.
*   `*.php` - User-facing pages (login, dashboard, application).

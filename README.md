# Electric Bike Loan Management System

## Project Overview
This project is an Electric Bike Loan Management System designed to help students or borrowers apply for loans to purchase electric bikes. Administrators can manage the inventory, approve/reject loans, and track repayments.

## Features

### For Borrowers (Students)
- **User Registration & Login:** Secure account creation and authentication.
- **Loan Application:** Apply for a loan by selecting a bike model, loan amount, and repayment duration.
- **Document Upload:** Upload necessary documents (ID Card, Utility Bill).
- **Dashboard:** View loan status and upcoming repayments.
- **Repayment:** Make payments towards the loan.
- **History:** View past repayments.

### For Administrators
- **Admin Dashboard:** Overview of system status.
- **Bike Management:** Add new electric bikes to the system.
- **Loan Management:** View, approve, or reject loan applications.
- **Repayment Tracking:** Monitor all repayments and overdue accounts.
- **Reports:** Generate reports on system activity.

## Setup Instructions

1.  **Prerequisites:**
    -   Web Server (e.g., Apache, Nginx)
    -   PHP (7.4 or higher recommended)
    -   MySQL Database

2.  **Database Setup:**
    -   Create a database named `ebike_loan_db`.
    -   Import the database schema (if provided) or ensure tables (`users`, `bikes`, `loan_applications`, `repayment_schedule`, `repayments`) are created as described in `docs/database_design.md`.
    -   Update `includes/db_connect.php` with your database credentials if necessary.

3.  **Run the Application:**
    -   Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP).
    -   Access the application via your browser (e.g., `http://localhost/your-project-folder/`).

4.  **Admin Access:**
    -   Register a user with the role 'admin' directly in the database (since the registration form defaults to 'borrower') OR update an existing user's role to 'admin' in the `users` table.

## Documentation
Detailed documentation can be found in the `docs/` directory:
-   [User Requirements](docs/user_requirements.md)
-   [Non-Functional Requirements](docs/non_functional_requirements.md)
-   [Use Case Diagram](docs/use_case_diagram.md)
-   [Use Case Descriptions](docs/use_case_descriptions.md)
-   [Behavioral Diagrams](docs/behavioral_diagrams.md)
-   [Structural Diagrams](docs/structural_diagrams.md)
-   [Database Design](docs/database_design.md)

## Tech Stack
-   **Backend:** Core PHP
-   **Database:** MySQL
-   **Frontend:** HTML, CSS, JavaScript

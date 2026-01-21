# Non-Functional Requirements

## 1. Security
- Passwords for borrowers shall be hashed before storage (though currently mixed implementation allows plain text for admin).
- Access to `admin/` pages shall be restricted to users with the 'admin' role.
- Access to borrower pages (e.g., `apply_loan.php`, `dashboard.php`) shall be restricted to logged-in users.
- File uploads shall be validated to ensure only allowed extensions (JPG, JPEG, PNG, PDF) are accepted.
- SQL injection prevention measures (using Prepared Statements) shall be used for database interactions.

## 2. Performance
- The system shall load pages within 3 seconds under normal network conditions.
- The system shall handle concurrent file uploads without data corruption.

## 3. Scalability
- The database design shall support the addition of new user roles or loan types without major schema changes.
- The file storage system (`uploads/`) shall be organized to handle a growing number of document submissions.

## 4. Reliability
- The system shall maintain data integrity for loan calculations and repayment schedules.
- Error messages shall be displayed clearly to users in case of failures (e.g., database connection errors, file upload errors).

## 5. Technology Stack
- **Backend:** PHP (Core, no framework).
- **Database:** MySQL.
- **Frontend:** HTML5, CSS3, JavaScript.
- **Server Environment:** Apache/Nginx (compatible with XAMPP/WAMP for development).

# Non-Functional Requirements

## Technical Constraints
- **Platform:** The system is a web-based application.
- **Language:** The backend is built using core PHP (no frameworks).
- **Database:** The system uses MySQL for data storage.
- **Frontend:** HTML, CSS, and basic JavaScript.
- **Server:** The application runs on a web server supporting PHP (e.g., Apache via XAMPP).

## Security
- **Authentication:** Users must authenticate via email and password to access protected features.
- **Password Storage:**
    - Borrower passwords should be hashed (implementation uses `password_verify` in `login.php`).
    - Admin passwords appear to be plain text based on `login.php` logic (Note: This is a security risk and should be improved in future iterations).
- **Input Validation:** The system performs basic input validation (e.g., required fields, email format).
- **File Uploads:** The system restricts file uploads to specific extensions (jpg, jpeg, png, pdf) to prevent malicious file execution.
- **Access Control:** Role-based access control (RBAC) ensures Borrowers cannot access Admin pages and vice versa.

## Performance
- The system should load pages quickly for a smooth user experience.
- Database queries should be optimized to handle multiple concurrent users.

## Usability
- The user interface should be intuitive and easy to navigate for both students and administrators.
- The system should provide clear feedback on actions (e.g., success messages, error alerts).

## Scalability
- The database schema is designed to support an increasing number of users, loans, and repayments.

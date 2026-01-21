# Non-Functional Requirements

## 1. Security
1.  **Authentication**: Passwords must be hashed using `password_hash()` (Bcrypt) before storage. Plain text passwords shall not be stored for borrowers.
2.  **Access Control**: Pages must be protected by session validation to ensure only authorized users (Borrowers or Admins) can access their respective areas.
3.  **Input Validation**: All user inputs (forms, file uploads) must be sanitized to prevent SQL Injection and Cross-Site Scripting (XSS).
4.  **File Security**: Uploaded documents (IDs, bills) must be restricted to specific file types (JPG, PNG, PDF) to prevent malicious code execution.

## 2. Performance
1.  **Response Time**: The system should load key pages (Dashboard, Application Form) within 2 seconds under normal load conditions.
2.  **Concurrency**: The system should support multiple concurrent users (e.g., 50+) accessing the dashboard without degradation.

## 3. Reliability & Availability
1.  **Data Integrity**: Financial transactions (repayments) must be processed within database transactions (COMMIT/ROLLBACK) to ensure consistency.
2.  **Uptime**: The system should be available 99.9% of the time during business hours.

## 4. Scalability
1.  **Database**: The database schema should be normalized to handle an increasing number of users and loan records without significant redesign.

## 5. Technology Stack
1.  **Backend**: Core PHP (Version 8.0+ recommended).
2.  **Database**: MySQL (MariaDB).
3.  **Frontend**: HTML5, CSS3, JavaScript.
4.  **Server**: Apache HTTP Server (via XAMPP for local dev).

# Non-Functional Requirements

## 1. Technical Constraints
*   **Language & Framework**: The system is built using **Core PHP** (no framework) for backend logic.
*   **Database**: The system uses **MySQL** (via `mysqli`) for data storage.
*   **Web Server**: The system is designed to run on a standard web server like **Apache** (e.g., XAMPP environment).
*   **Frontend**: The user interface is built with **HTML, CSS, and vanilla JavaScript**.

## 2. Security
*   **Authentication**: Session-based authentication is used to manage user login states (`session_start()`).
*   **Access Control**: Role-based access control (RBAC) ensures that only Admins can access admin pages and Borrowers can access user pages.
*   **Password Storage**: The system supports both plain text (legacy/admin) and hashed passwords (`password_verify`), though registration currently uses plain text (as per code analysis of `register.php`, likely for simplicity or specific requirements, but typically should be hashed).
*   **File Upload Validation**: The system validates file extensions (jpg, jpeg, png, pdf) before allowing uploads.

## 3. Performance & Scalability
*   The application is designed for a moderate number of users, suitable for a campus or local organization setting.
*   Database queries are direct and simple, ensuring low latency for standard operations.
*   File uploads are stored in a local directory (`uploads/`), which is simple but may require migration to cloud storage (e.g., S3) for high scalability.

## 4. Usability
*   **Responsive Design**: The application uses CSS (e.g., `assets/css/style.css`) to ensure it is usable on different device sizes.
*   **Feedback**: The system provides immediate feedback via JavaScript alerts for actions like registration success, loan application submission, and payment confirmation.

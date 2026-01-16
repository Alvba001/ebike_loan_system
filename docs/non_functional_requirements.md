# Non-Functional Requirements

## 1. Security
- **Authentication:** Passwords should be hashed before storage (currently indicated in code but needs enforcement).
- **Access Control:** The system must enforce role-based access control (RBAC) to ensure Borrowers cannot access Admin functions and vice-versa.
- **Data Protection:** Sensitive documents (ID Cards, Utility Bills) should be stored securely and access restricted to authorized personnel.
- **Input Validation:** All user inputs must be validated and sanitized to prevent SQL injection and XSS attacks.
- **Secure Payments:** Payment processing must be handled securely via the Paystack API, ensuring PCI compliance.

## 2. Performance
- **Response Time:** The system should load pages and process requests within 2 seconds under normal load.
- **Database Optimization:** Database queries should be optimized (indexes on `user_id`, `loan_id`) to handle concurrent users efficiently.

## 3. Scalability
- **Modular Design:** The system should be built with modularity in mind to allow for future addition of features (e.g., more bike models, new payment gateways).
- **Storage:** The file upload system should be capable of handling increasing numbers of document uploads.

## 4. Reliability & Availability
- **Uptime:** The system should aim for 99.9% availability during business hours.
- **Data Integrity:** Transactional integrity must be maintained, especially for loan creation and repayment recording.

## 5. Technology Stack
- **Backend:** PHP (Native/Core)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Payment Gateway:** Paystack API
- **Server:** Apache (via XAMPP for local development)

# User Requirements

## 1. Borrowers (Students/Users)

### Registration & Authentication
- The Borrower shall be able to register for an account by providing their name, email, and password.
- The Borrower shall be able to log in to the system using their registered email and password.
- The Borrower shall be able to log out of the system.

### Loan Application
- The Borrower shall be able to view available e-bike models and their loan amounts.
- The Borrower shall be able to apply for a loan by selecting a repayment duration (3, 6, or 12 months).
- The Borrower shall be able to provide their NIN (National Identification Number) during application.
- The Borrower shall be able to upload required documents (ID Card, Utility Bill) in supported formats (JPG, PNG, PDF).
- The Borrower shall be able to specify the purpose of the loan.

### Dashboard & Status
- The Borrower shall be able to view their dashboard with a summary of their loan status.
- The Borrower shall be able to view the status of their submitted loan application (Pending, Approved, Rejected).
- The Borrower shall be able to view notifications related to their loan.

### Repayment
- The Borrower shall be able to view their repayment schedule, including due dates and amounts.
- The Borrower shall be able to view their repayment history, including dates and amounts paid.
- The Borrower shall be able to make installment payments securely via Paystack.
- The Borrower shall be able to see their remaining loan balance.

## 2. Administrators

### Authentication
- The Admin shall be able to log in to the admin panel.
- The Admin shall be able to log out of the admin panel.

### Loan Management
- The Admin shall be able to view a list of all loan applications.
- The Admin shall be able to view detailed information for each loan application, including uploaded documents.
- The Admin shall be able to approve a loan application, which generates a repayment schedule.
- The Admin shall be able to reject a loan application.

### Monitoring & Reporting
- The Admin shall be able to view a list of all repayments made by borrowers.
- The Admin shall be able to view system reports (implied by `reports.php`).
- The Admin shall be able to check for overdue payments and manage reminders (implied by `check_reminders.php`).

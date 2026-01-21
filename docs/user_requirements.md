# User Requirements

## 1. Functional Requirements

### 1.1 Borrowers (Students/Users)
The Borrower shall be able to:
1.  **Register Account**: Create a new account by providing full name, email, and password.
2.  **Login**: Access their account using email and password.
3.  **View Dashboard**: See an overview of their loan status and pending repayments.
4.  **Apply for Loan**: Submit a loan application by selecting an electric bike model, loan duration, and providing personal details (NIN, BVN).
5.  **Upload Documents**: Upload digital copies of their ID Card and Utility Bill during the application process.
6.  **Provide Guarantor Details**: Submit information about a guarantor (name, contact, relationship, etc.) to support their loan application.
7.  **View Loan Status**: Check if their application is Pending, Approved, or Rejected.
8.  **View Repayment Schedule**: See the breakdown of monthly installments for approved loans.
9.  **Make Repayment**: Pay the due installment amount (simulated payment).
10. **View Repayment History**: Access a log of all past payments made.
11. **Update Profile**: (Implied) Maintain accurate personal contact information.

### 1.2 Administrators
The Administrator shall be able to:
1.  **Login**: Access the administrative dashboard.
2.  **View Dashboard**: See a summary of total loans, pending applications, and recent activities.
3.  **Manage Loans**: View a list of all loan applications with their current status.
4.  **Review Applications**: Inspect detailed application data, including uploaded ID and Utility Bill documents.
5.  **Approve Loans**: Approve a loan application, which automatically generates a repayment schedule based on the chosen duration and interest rate.
6.  **Reject Loans**: Reject a loan application.
7.  **View Repayments**: Monitor the list of repayments made by borrowers.
8.  **Generate Reports**: Access reports regarding loan performance and system usage.
9.  **Check Reminders**: (Implied from file structure) View or send reminders for due payments.

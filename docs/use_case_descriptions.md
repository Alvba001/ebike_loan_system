# Use Case Descriptions

## 1. Apply for Loan

| Field | Description |
|---|---|
| **Actor** | Borrower |
| **Description** | The borrower submits an application for an e-bike loan, providing necessary details and documents. |
| **Preconditions** | Borrower must be logged in. |
| **Post-conditions** | A new loan application record is created with status 'Pending'. Documents are uploaded. |
| **Main Flow** | 1. Borrower navigates to 'Apply Loan' page.<br>2. Borrower selects bike model and loan duration.<br>3. Borrower enters NIN and purpose.<br>4. Borrower uploads ID Card and Utility Bill.<br>5. Borrower submits the form.<br>6. System validates input and uploads files.<br>7. System saves application and shows success message. |
| **Alternative Flows** | - Invalid file format: System displays error and requests valid file.<br>- Missing fields: System prompts user to fill all required fields. |

## 2. Approve Loan

| Field | Description |
|---|---|
| **Actor** | Admin |
| **Description** | The admin reviews and approves a pending loan application. |
| **Preconditions** | Admin is logged in. Pending loan application exists. |
| **Post-conditions** | Loan status updates to 'Approved'. Repayment schedule is generated. Notification sent (optional). |
| **Main Flow** | 1. Admin views list of pending loans.<br>2. Admin selects a loan to view details.<br>3. Admin verifies documents and eligibility.<br>4. Admin clicks 'Approve'.<br>5. System updates status and generates monthly repayment schedule. |

## 3. Make Repayment

| Field | Description |
|---|---|
| **Actor** | Borrower |
| **Description** | The borrower pays a scheduled installment using the payment gateway. |
| **Preconditions** | Borrower has an approved loan with pending scheduled payments. |
| **Post-conditions** | Payment recorded in 'Repayments' table. Schedule item marked 'Paid'. Loan balance decreases. |
| **Main Flow** | 1. Borrower views repayment schedule.<br>2. Borrower initiates payment for the next due installment.<br>3. System redirects to Paystack.<br>4. Borrower completes payment.<br>5. Paystack redirects back with success status.<br>6. System verifies transaction and updates records. |
| **Alternative Flows** | - Payment Failed: System displays error message and schedule remains 'Pending'. |

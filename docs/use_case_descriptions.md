# Use Case Descriptions

## 1. Apply for Loan
| Field | Description |
|---|---|
| **Actor** | Borrower |
| **Description** | The borrower submits an application for an electric bike loan, providing necessary details and documents. |
| **Preconditions** | User must be logged in as a 'borrower'. |
| **Post-conditions** | A new record is created in `loan_applications` with status 'pending'. Documents are saved to the server. |
| **Main Flow** | 1. Borrower navigates to "Apply for Loan".<br>2. Borrower selects bike model, duration, and enters NIN/Purpose.<br>3. Borrower uploads ID, Utility Bill, Guarantor Doc.<br>4. Borrower submits form.<br>5. System validates inputs and file types.<br>6. System saves application. |

## 2. Approve Loan
| Field | Description |
|---|---|
| **Actor** | Admin |
| **Description** | The admin reviews and approves a pending loan application. |
| **Preconditions** | User must be logged in as 'admin'. A loan application with status 'pending' exists. |
| **Post-conditions** | Loan status updates to 'approved'. Repayment schedule is generated in `repayment_schedule`. |
| **Main Flow** | 1. Admin views list of loans.<br>2. Admin selects a loan to view details.<br>3. Admin clicks "Approve Loan".<br>4. System updates status and generates monthly repayment entries. |

## 3. Make Repayment
| Field | Description |
|---|---|
| **Actor** | Borrower |
| **Description** | The borrower makes a payment towards their approved loan. |
| **Preconditions** | Borrower has an approved loan and a pending repayment schedule. |
| **Post-conditions** | A record is added to `repayments`. The specific `repayment_schedule` entry is marked as 'paid'. |
| **Main Flow** | 1. Borrower navigates to "Make Repayment".<br>2. System displays next due amount.<br>3. Borrower confirms payment (simulated).<br>4. System records transaction and updates schedule status. |

## 4. Assign Bike
| Field | Description |
|---|---|
| **Actor** | Admin |
| **Description** | The admin assigns a specific physical bike from inventory to an approved loan. |
| **Preconditions** | Loan is approved. Bikes with status 'available' exist in inventory. |
| **Post-conditions** | A record is added to `bike_assignments`. Bike status updates to 'assigned'. |
| **Main Flow** | 1. Admin selects an approved loan.<br>2. Admin clicks "Assign Bike".<br>3. Admin selects a bike from the dropdown.<br>4. System links bike to loan and updates bike status. |

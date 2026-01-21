# Use Case Descriptions

## 1. Apply for Loan
| Field | Description |
|---|---|
| **Actor** | Borrower |
| **Description** | The borrower submits a request for a loan to purchase an e-bike. |
| **Preconditions** | User must be logged in and not have an active pending or approved loan (unless multiple loans are allowed). |
| **Post-conditions** | A new loan record is created with status 'pending_guarantor' (initially) and then 'pending' after guarantor info is added. |
| **Flow** | 1. User selects bike model, amount, and duration.<br>2. User enters NIN and BVN.<br>3. User uploads ID Card and Utility Bill.<br>4. System saves initial details.<br>5. User provides Guarantor Information.<br>6. System updates status to 'pending'. |

## 2. Approve Loan
| Field | Description |
|---|---|
| **Actor** | Admin |
| **Description** | The admin reviews and approves a pending loan application. |
| **Preconditions** | A loan application with status 'pending' exists. Admin is logged in. |
| **Post-conditions** | Loan status updates to 'approved'. Repayment schedule is generated. |
| **Flow** | 1. Admin views loan details.<br>2. Admin clicks 'Approve'.<br>3. System calculates interest based on duration.<br>4. System generates monthly repayment records in `repayment_schedule`.<br>5. Loan status is updated. |

## 3. Register
| Field | Description |
|---|---|
| **Actor** | Guest |
| **Description** | A new user creates an account to access the system. |
| **Preconditions** | User does not have an account with the provided email. |
| **Post-conditions** | A new user record is created in the database. |
| **Flow** | 1. Guest enters Name, Email, and Password.<br>2. System validates input.<br>3. System checks for existing email.<br>4. System creates account and redirects to login. |

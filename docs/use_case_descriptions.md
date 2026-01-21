# Use Case Descriptions

## 1. Login
| Field | Description |
| :--- | :--- |
| **Actor** | Borrower, Admin |
| **Description** | Allows users to authenticate and access the system. |
| **Preconditions** | User must have a registered account. |
| **Post-conditions** | User is redirected to their respective dashboard (Borrower or Admin). |

## 2. Apply for Loan
| Field | Description |
| :--- | :--- |
| **Actor** | Borrower |
| **Description** | User submits a request for an electric bike loan. |
| **Preconditions** | User is logged in; User does not have an active pending/approved loan (optional constraint). |
| **Post-conditions** | Loan application is created with status "Pending Guarantor" (or Pending). |

## 3. Upload Documents
| Field | Description |
| :--- | :--- |
| **Actor** | Borrower |
| **Description** | User uploads ID Card and Utility Bill as proof of identity/address. |
| **Preconditions** | User is filling out the loan application form. |
| **Post-conditions** | Files are saved to the server and paths recorded in the database. |

## 4. Approve Loan
| Field | Description |
| :--- | :--- |
| **Actor** | Admin |
| **Description** | Admin reviews and approves a pending loan application. |
| **Preconditions** | Loan status is "Pending"; Admin is logged in. |
| **Post-conditions** | Loan status updates to "Approved"; Repayment schedule is generated. |

## 5. Make Repayment
| Field | Description |
| :--- | :--- |
| **Actor** | Borrower |
| **Description** | Borrower pays a scheduled installment. |
| **Preconditions** | Loan is "Approved"; A repayment schedule exists with "Pending" status. |
| **Post-conditions** | Payment record created; Schedule marked as "Paid"; Balance updated. |

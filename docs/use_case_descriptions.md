# Use Case Descriptions

## 1. Apply for Loan
| Attribute | Description |
| :--- | :--- |
| **Actor** | Borrower |
| **Description** | The Borrower fills out a form to apply for an electric bike loan, selecting a model and uploading documents. |
| **Preconditions** | Borrower is logged in. |
| **Post-conditions** | A new loan application record is created with status 'Pending'. |

## 2. Make Repayment
| Attribute | Description |
| :--- | :--- |
| **Actor** | Borrower |
| **Description** | The Borrower pays an installment towards their approved loan. |
| **Preconditions** | Borrower is logged in and has an approved loan with pending payments. |
| **Post-conditions** | A repayment record is created, and the schedule status is updated to 'Paid'. |

## 3. Approve/Reject Loan
| Attribute | Description |
| :--- | :--- |
| **Actor** | Admin |
| **Description** | The Admin reviews a loan application and decides to approve or reject it. |
| **Preconditions** | Admin is logged in and there are pending loan applications. |
| **Post-conditions** | The loan application status is updated to 'Approved' or 'Rejected'. If approved, a repayment schedule is generated. |

## 4. Add Bike
| Attribute | Description |
| :--- | :--- |
| **Actor** | Admin |
| **Description** | The Admin adds a new bike model and serial number to the system inventory. |
| **Preconditions** | Admin is logged in. |
| **Post-conditions** | A new bike record is added to the database. |

## 5. View Repayments (Admin)
| Attribute | Description |
| :--- | :--- |
| **Actor** | Admin |
| **Description** | The Admin views a list of all repayments made by borrowers. |
| **Preconditions** | Admin is logged in. |
| **Post-conditions** | The list of repayments is displayed. |

# Use Case Diagram

```mermaid
usecaseDiagram
    actor Borrower
    actor Admin

    package "Loan Management System" {
        usecase "Register" as UC1
        usecase "Login" as UC2
        usecase "Apply for Loan" as UC3
        usecase "View Loan Status" as UC4
        usecase "Make Repayment" as UC5
        usecase "View Repayment History" as UC6
        usecase "Add Bike" as UC7
        usecase "View All Loans" as UC8
        usecase "Approve/Reject Loan" as UC9
        usecase "View Repayments" as UC10
        usecase "Generate Reports" as UC11
    }

    Borrower --> UC1
    Borrower --> UC2
    Borrower --> UC3
    Borrower --> UC4
    Borrower --> UC5
    Borrower --> UC6

    Admin --> UC2
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
```

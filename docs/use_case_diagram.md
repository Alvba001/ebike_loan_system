# Use Case Diagram

```mermaid
usecaseDiagram
    actor Borrower
    actor Admin

    package "Loan Management System" {
        usecase "Register/Login" as UC1
        usecase "Apply for Loan" as UC2
        usecase "View Dashboard" as UC3
        usecase "Make Repayment" as UC4
        usecase "View Repayment History" as UC5

        usecase "Manage Bikes (Add/View)" as UC6
        usecase "View Loan Applications" as UC7
        usecase "Approve/Reject Loan" as UC8
        usecase "Assign Bike" as UC9
        usecase "View All Repayments" as UC10
        usecase "Check Reminders" as UC11
    }

    Borrower --> UC1
    Borrower --> UC2
    Borrower --> UC3
    Borrower --> UC4
    Borrower --> UC5

    Admin --> UC1
    Admin --> UC3
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
```

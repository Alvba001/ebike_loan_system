# Use Case Diagram

```mermaid
usecaseDiagram
    actor Borrower
    actor Admin

    package "Authentication" {
        usecase "Register" as UC1
        usecase "Login" as UC2
        usecase "Logout" as UC3
    }

    package "Loan Management" {
        usecase "Apply for Loan" as UC4
        usecase "View Loan Status" as UC5
        usecase "Upload Documents" as UC6
        usecase "Approve Loan" as UC7
        usecase "Reject Loan" as UC8
        usecase "View All Loans" as UC9
    }

    package "Repayment System" {
        usecase "View Repayment Schedule" as UC10
        usecase "Make Repayment" as UC11
        usecase "View Repayment History" as UC12
        usecase "View All Repayments" as UC13
    }

    Borrower --> UC1
    Borrower --> UC2
    Borrower --> UC3
    Borrower --> UC4
    Borrower --> UC5
    Borrower --> UC10
    Borrower --> UC11
    Borrower --> UC12

    UC4 ..> UC6 : <<include>>

    Admin --> UC2
    Admin --> UC3
    Admin --> UC9
    Admin --> UC7
    Admin --> UC8
    Admin --> UC13
```

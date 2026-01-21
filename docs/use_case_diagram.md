# Use Case Diagram

```mermaid
usecaseDiagram
    actor "Borrower" as B
    actor "Admin" as A
    actor "Guest" as G

    usecase "Register" as UC1
    usecase "Login" as UC2
    usecase "View Dashboard" as UC3
    usecase "Apply for Loan" as UC4
    usecase "Upload Documents" as UC5
    usecase "Provide Guarantor Info" as UC6
    usecase "View Loan Status" as UC7
    usecase "View Repayment Schedule" as UC8
    usecase "Make Repayment" as UC9

    usecase "View All Loans" as UC10
    usecase "Approve Loan" as UC11
    usecase "Reject Loan" as UC12
    usecase "View Repayments" as UC13

    G --> UC1
    G --> UC2

    B --> UC2
    B --> UC3
    B --> UC4
    B --> UC7
    B --> UC8
    B --> UC9

    UC4 ..> UC5 : <<include>>
    UC4 ..> UC6 : <<include>>

    A --> UC2
    A --> UC10
    A --> UC11
    A --> UC12
    A --> UC13
```

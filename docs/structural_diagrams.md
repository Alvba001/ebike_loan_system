# Structural Diagrams

## Class Diagram (Logical Module Structure)

Since the application is built with procedural PHP, this diagram represents the logical entities and their relationships as if they were classes, reflecting the data model and associated logic modules.

```mermaid
classDiagram
    class User {
        +int user_id
        +string name
        +string email
        +string password
        +string role
        +register()
        +login()
    }

    class LoanApplication {
        +int loan_id
        +int user_id
        +string bike_model
        +float amount
        +int duration
        +string status
        +string id_card_path
        +string utility_bill_path
        +apply()
        +approve()
        +reject()
    }

    class RepaymentSchedule {
        +int schedule_id
        +int loan_id
        +date due_date
        +float amount_due
        +string status
        +generateSchedule()
        +markAsPaid()
    }

    class Repayment {
        +int repayment_id
        +int loan_id
        +float amount_paid
        +date date_paid
        +recordPayment()
    }

    class Admin {
        +login()
        +viewAllLoans()
        +manageLoan()
    }

    User "1" --> "0..*" LoanApplication : makes
    LoanApplication "1" *-- "1..*" RepaymentSchedule : has
    LoanApplication "1" --> "0..*" Repayment : receives
    Admin ..> LoanApplication : manages
```

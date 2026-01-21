# Structural Diagrams

## Class Diagram (Conceptual)
Since the system is built with procedural PHP, this class diagram represents the logical modules and their relationships rather than strict OOP classes.

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
        +string status
        +float amount
        +string bike_model
        +apply()
        +uploadDocs()
    }

    class Guarantor {
        +int id
        +int loan_id
        +string name
        +string phone
        +addDetails()
    }

    class Admin {
        +approveLoan()
        +rejectLoan()
        +viewReports()
    }

    class Repayment {
        +int id
        +int loan_id
        +float amount_paid
        +date date_paid
        +makePayment()
    }

    class RepaymentSchedule {
        +int schedule_id
        +int loan_id
        +date due_date
        +float amount_due
        +string status
        +generate()
    }

    User "1" -- "*" LoanApplication : applies
    LoanApplication "1" -- "1" Guarantor : has
    LoanApplication "1" -- "*" RepaymentSchedule : has
    LoanApplication "1" -- "*" Repayment : receives
    Admin ..> LoanApplication : manages
```

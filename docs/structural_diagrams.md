# Structural Diagrams

## Class Diagram
This diagram represents the conceptual classes and their relationships as inferred from the database schema and application logic.

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
        +float amount
        +string bike_model
        +int duration
        +string status
        +date date_applied
        +apply()
        +approve()
        +reject()
    }

    class Bike {
        +int bike_id
        +string serial_number
        +string model
        +string status
        +add()
        +assign()
    }

    class RepaymentSchedule {
        +int schedule_id
        +int loan_id
        +date due_date
        +float amount_due
        +string status
    }

    class Repayment {
        +int repayment_id
        +int loan_id
        +float amount_paid
        +date payment_date
    }

    class Notification {
        +int notification_id
        +int user_id
        +string message
    }

    User "1" --> "*" LoanApplication : applies for
    User "1" --> "*" Notification : receives
    LoanApplication "1" --> "*" RepaymentSchedule : generates
    LoanApplication "1" --> "*" Repayment : receives
    LoanApplication "1" --> "0..1" Bike : assigned
```

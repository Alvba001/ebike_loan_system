# Structural Diagrams

## Class/Module Diagram

Since the application uses core PHP (procedural/script-based) rather than a strict Object-Oriented structure, this diagram represents the relationship between key modules (files) and the database.

```mermaid
classDiagram
    class Database {
        +connect()
        +query()
    }

    class AuthModule {
        +register.php
        +login.php
        +logout.php
    }

    class UserModule {
        +dashboard.php
        +profile.php
        +apply_loan.php
        +guarantor_info.php
        +repayment_schedule.php
    }

    class AdminModule {
        +admin/dashboard.php
        +admin/view_loans.php
        +admin/approve_loan.php
        +admin/reject_loan.php
        +admin/view_repayments.php
    }

    class Utilities {
        +uploads/
        +assets/
    }

    AuthModule ..> Database : Uses
    UserModule ..> Database : Uses
    AdminModule ..> Database : Uses
    UserModule ..> Utilities : Uploads Files
    UserModule --> AuthModule : Redirects if not logged in
    AdminModule --> AuthModule : Redirects if not logged in
```

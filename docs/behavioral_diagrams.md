# Behavioral Diagrams

## 1. Sequence Diagram: Loan Application Process
This diagram illustrates the interaction between the Borrower, the System, and the Database during a loan application.

```mermaid
sequenceDiagram
    participant B as Borrower
    participant S as System
    participant DB as Database

    B->>S: Request Loan Application Form
    S-->>B: Display Form (Model, Duration, Uploads)

    B->>S: Submit Form (NIN, BVN, Files)
    S->>S: Validate Inputs & File Types

    alt Validation Failed
        S-->>B: Show Error Message
    else Validation Success
        S->>S: Upload Files to Server
        S->>DB: INSERT Loan Application
        DB-->>S: Return New Loan ID
        S-->>B: Redirect to Guarantor Form

        B->>S: Submit Guarantor Details
        S->>DB: INSERT Guarantor Info
        S->>DB: UPDATE Loan Status = 'Pending'
        S-->>B: Show "Application Submitted" Success
    end
```

## 2. Activity Diagram: Loan Approval Process
This diagram shows the flow of actions when an Admin reviews a loan.

```mermaid
flowchart TD
    A[Start] --> B{Admin Logs In}
    B -- Success --> C[View Pending Loans]
    C --> D[Select Loan Application]
    D --> E[Review Documents & Guarantor]

    E --> F{Decision?}

    F -- Approve --> G[Update Status to 'Approved']
    G --> H[Calculate Interest]
    H --> I[Generate Repayment Schedule]
    I --> J[Notify Borrower (Simulated)]

    F -- Reject --> K[Update Status to 'Rejected']
    K --> L[Notify Borrower (Simulated)]

    J --> M[End]
    L --> M
```

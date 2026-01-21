# Chapter Four: Implementation and Testing

## 4.1 Introduction
This chapter discusses the implementation and testing phase of the Electric Bike Loan Management System. It details the tools used for development, the algorithms for major functionalities, and the system's operation. Furthermore, it outlines the testing strategies employed, including unit, integration, and system testing, to ensure the software meets the specified requirements and functions correctly.

## 4.2 Implementation

### 4.2.1 Implementation Tools
The system was developed using a standard LAMP stack (Linux, Apache, MySQL, PHP) environment, though simulated on a local machine using XAMPP. The specific tools chosen include:

*   **Operating System**: Windows 11 (Development Environment).
*   **Web Server**: Apache HTTP Server (via XAMPP) – Chosen for its robustness and seamless integration with PHP.
*   **Server-Side Language**: PHP 8.2 – Selected for its strong support for web development and database integration.
*   **Database**: MySQL (MariaDB via XAMPP) – A reliable relational database management system used for storing user and loan data.
*   **Frontend Technologies**: HTML5, CSS3, JavaScript – Used to create a responsive and interactive user interface.
*   **Development Environment (IDE)**: Visual Studio Code – Chosen for its lightweight nature and extensive extension support for PHP and web technologies.
*   **Browser**: Google Chrome – Used for debugging and testing the user interface.

### 4.2.2 Algorithms of major functionality
This section presents the logic for the critical functions of the system: User Login, Loan Application, and Loan Approval (with Repayment Schedule Generation).

**Algorithm 1: User Login**
```text
1. Start
2. Receive Input: Email, Password
3. Sanitize inputs
4. Query Database: SELECT * FROM users WHERE email = Input_Email
5. IF (User Found):
6.    Fetch stored password hash
7.    IF (Verify(Input_Password, Stored_Hash) is TRUE):
8.       Set Session Variables (User_ID, Role, Name)
9.       IF (Role == 'admin'):
10.         Redirect to Admin Dashboard
11.      ELSE:
12.         Redirect to Borrower Dashboard
13.   ELSE:
14.      Display "Invalid Password" Error
15. ELSE:
16.    Display "No Account Found" Error
17. Stop
```

**Algorithm 2: Loan Application**
```text
1. Start
2. Check Session: IF User not logged in, Redirect to Login
3. Receive Input: Loan Details (Amount, Bike Model, Duration), Personal Info (NIN, BVN), Files (ID Card, Utility Bill)
4. Validate Inputs: Check if fields are empty, validate file extensions (JPG, PNG, PDF)
5. Upload Files:
6.    Move uploaded ID Card to 'uploads/' directory
7.    Move uploaded Utility Bill to 'uploads/' directory
8.    IF (Upload Fails): Return Error
9. Insert Data: INSERT INTO loan_applications (user_id, amount, bike_model, duration, status='pending', ...)
10. IF (Insert Success):
11.    Get new Loan_ID
12.    Redirect to Guarantor Information Page
13. ELSE:
14.    Display Database Error
15. Stop
```

**Algorithm 3: Admin Loan Approval & Schedule Generation**
```text
1. Start (Admin Triggered)
2. Get Loan_ID from request
3. Fetch Loan Details (Amount, Duration)
4. Calculate Interest based on Duration:
5.    IF (Duration == 3) Rate = 1.10
6.    ELSE IF (Duration == 6) Rate = 1.15
7.    ELSE IF (Duration == 12) Rate = 1.20
8. Calculate Total_Payable = Amount * Rate
9. Calculate Monthly_Payment = Total_Payable / Duration
10. Update Database: SET status = 'approved' WHERE id = Loan_ID
11. Generate Schedule (Loop i = 1 to Duration):
12.    Calculate Due_Date = Current_Date + i months
13.    INSERT INTO repayment_schedule (loan_id, due_date, amount_due, status='pending')
14. End Loop
15. Notify Admin "Loan Approved and Schedule Generated"
16. Stop
```

### 4.2.3 Description of System Operation
This section describes how the system operates from the user's perspective.

**1. Landing & Registration**
The user lands on the Home Page and can navigate to the Registration page. Here, they enter their full name, email, and password. The system checks if the email is unique before creating the account.
![Screenshot of Registration Page](screenshots/register_page.png)

**2. User Dashboard**
Upon logging in, the borrower is presented with a dashboard showing their active loans and options to apply for a new loan.
![Screenshot of User Dashboard](screenshots/user_dashboard.png)

**3. Loan Application**
The user selects a bike model and loan tenure. They must upload valid identification documents. The system validates these inputs before proceeding to the Guarantor form.
![Screenshot of Loan Application Form](screenshots/application_form.png)

**4. Admin Dashboard**
The administrator sees a high-level view of all applications. They can drill down into specific loan requests to view documents and approve or reject them.
![Screenshot of Admin Dashboard](screenshots/admin_dashboard.png)

## 4.3 Testing

### 4.3.1 Unit Testing
Unit testing focused on verifying individual components and functions.

| Test Case ID | Unit | Test Description | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| UT-01 | Database Connect | Verify connection to MySQL | Connection object created | Connection successful | Pass |
| UT-02 | Input Validation | Submit empty fields in Registration | Alert "All fields required" | Alert displayed | Pass |
| UT-03 | File Upload | Upload .exe file as ID Card | Error "Invalid file type" | Error displayed | Pass |
| UT-04 | Interest Calc | Calculate 3-month interest | Amount * 1.10 | Correct calculation | Pass |

### 4.3.3 Integration Testing
Integration testing verified that different modules work together correctly (e.g., Application Form + Database + Admin View).

| Test Case ID | Modules | Test Description | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| IT-01 | Register -> Login | Register new user then try to login | Login successful | User logged in | Pass |
| IT-02 | Apply -> Database | Submit loan application | Data visible in Admin Dashboard | Data visible | Pass |
| IT-03 | Approval -> Schedule | Admin approves loan | Repayment rows created in DB | Rows created | Pass |
| IT-04 | Repayment -> History | User makes payment | Balance updates in history | Balance updated | Pass |

### 4.3.4 System Testing
System testing involved validating the complete end-to-end flow of the application.
1.  **Scenario**: A new user registers, applies for an EV loan (uploading valid docs), adds a guarantor, and waits.
2.  **Action**: Admin logs in, reviews the files, and approves the loan.
3.  **Result**: The user sees the status change to "Approved" and can view the generated repayment schedule. The system successfully transitioned the state across multiple sessions and user roles without error.

### 4.3.5 Usability Testing
A usability test was conducted with a small group of 5 users (students and staff) to evaluate the system's ease of use. They were asked to perform tasks such as "Register an account" and "Apply for a loan".
*   **Results**: 100% of users completed the registration within 2 minutes. 80% found the file upload process intuitive.
*   **Feedback**: Some users suggested adding a progress bar for the application steps.

## 4.4 Summary
This chapter detailed the implementation of the Electric Bike Loan Management System, highlighting the use of PHP and MySQL. Algorithms for the core processes—registration, application, and approval—were provided. The testing phase demonstrated that the system is functional, robust, and user-friendly, passing unit, integration, and system-level checks. The next chapter will conclude the project and offer recommendations.

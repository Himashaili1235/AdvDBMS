# Secure Student Grading Management System (SGS)

## Description
The **Secure Student Grading Management System (SGS)** is a web-based platform designed to securely manage student grades with a focus on **data integrity, encryption, and access control**. The system implements **role-based authentication (RBAC), SHA-256 password hashing, and audit logging** to prevent unauthorized access, data breaches, and manual errors.

---

## Features

### 🔒 Secure Authentication & Access Control
- **SHA-256 Hashing with Salt** for secure password storage.
- **Multi-Factor Authentication (MFA)** enhances security.
   - Using Mailtrap for testing OTP authentication. https://mailtrap.io
   - After logging in copy the host, username, password in the code login.php.
   - Now when user tries to login in SGA it would send the verification OTP to mailtrap.io
- **Role-Based Access Control (RBAC):**
  - **Students:** View grades only.
  - **Professors:** Input and update grades.
  - **Admins:** Manage system operations.

### 📊 Audit Logging & Monitoring
- Tracks **all user actions** (logins, grade updates, unauthorized access attempts).
- **Real-time alerts** for suspicious activities.
- **Automated log analysis** detects unusual login patterns.
- **Periodic security audits** ensure compliance with best practices.

### 🛡️ SQL Injection Prevention
- Uses **prepared statements** to prevent SQL injection attacks.
- Restricts **access permissions** based on roles.

---

## 🛠 Tech Stack

| Component  | Technology  |
|------------|------------|
| **Frontend** | HTML, CSS, JavaScript |
| **Backend** | PHP |
| **Database** | MySQL (MySQL Workbench) |
| **Development Tools** | Visual Studio Code | XAMPP |

---

## 📂 Database Structure

| Table  | Columns  |
|--------|---------|
| **Users** | UserID, Name, Role (Student, Professor, Admin), Email, Password, otpCode, otp_expires |
| **Grades** | GradeID, StudentID, CourseID, Grade, EncryptedGrade, grade_label |
| **roles** | role_id, role_name |
| **AuditLog** | LogID, UserID, Action, Timestamp, recordID, timestamp |
| **Courses** | CourseID, CourseName, CourseCode, ProfessorID, created_at |
| **Enrollments** | enrollmentID, studentID, courseID, enrolledAt |
| **GradeOptions** | gradeID, gradeLabel |

---

## 🚀 Setup Instructions

### Prerequisites.
1. Install **XAMPP** for the local server environment.
2. Install **Visual Studio Code** or any preferred IDE.

### Database Configuration
1. Import the provided **SQL file** into MySQL Workbench to create the necessary tables.
2. Update database connection settings in the backend:
   $host = 'localhost';
  $username = 'root';
  $password = '';
  $database = 'sga';


## Running the Application
- Start Apache and MySQL services in XAMPP.
- Place the project folder in the htdocs directory.
- Open the application in your browser at http://localhost/sga/

---

## 🔮 Future Enhancements
- **Blockchain Integration:** Securely manage data transactions and records.
- **AI-Based Anomaly Detection:** Identify unusual grading patterns.
- **Mobile Application:** Provide access on-the-go for students and faculty.
- **Geolocation-Based Alerts:** Secure access based on device locations. 
- **Add Notficatiobs:** Send alerts for new grades, logins or any system changes.
- **Improve Dashboard:** Show charts and stats about student performance.
- **AES Data Encryption:** secures student grades from unauthorized access.

---




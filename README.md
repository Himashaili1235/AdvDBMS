# Secure Student Grading Management System (SGS)

## Description
The **Secure Student Grading Management System (SGS)** is a web-based platform designed to securely manage student grades with a focus on **data integrity, encryption, and access control**. The system implements **role-based authentication (RBAC), SHA-256 password hashing, AES encryption, and audit logging** to prevent unauthorized access, data breaches, and manual errors.

---

## Features

### 🔒 Secure Authentication & Access Control
- **SHA-256 Hashing with Salt** for secure password storage.
- **Multi-Factor Authentication (MFA)** enhances security.
- **Role-Based Access Control (RBAC):**
  - **Students:** View grades only.
  - **Professors:** Input and update grades.
  - **Admins:** Manage system operations.

### 🔐 Data Encryption for Protection
- **AES-256 Encryption** secures student grades from unauthorized access.
- **Data-at-Rest Encryption:** Grades stored in encrypted format.
- **Data-in-Transit Encryption (TLS/SSL):** Prevents interception during transmission.
- **Implementation:**
  ```sql
  -- Encrypting data
  AES_ENCRYPT('A', 'key')
  
  -- Decrypting data
  AES_DECRYPT(grade, 'key')
  ```

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
| **Backend** | Node.js, Express |
| **Database** | MySQL (MySQL Workbench) |
| **Development Tools** | Visual Studio Code |

---

## 📂 Database Structure

| Table  | Columns  |
|--------|---------|
| **Users** | UserID, Name, Role (Student, Professor, Admin), Email, Password |
| **Grades** | GradeID, StudentID, CourseID, Grade, EncryptedGrade |
| **AuditLog** | LogID, UserID, Action, Timestamp |
| **Courses** | CourseID, CourseName, ProfessorID |

---

## 🚀 Setup Instructions

### Prerequisites
1. Install **MySQL Workbench** for database management.
2. Install **Node.js** and **npm**.
3. Install **Visual Studio Code** or any preferred IDE.

### Database Configuration
1. Import the provided **SQL file** into MySQL Workbench to create the necessary tables.
2. Update database connection settings in the backend:
   ```javascript
   const mysql = require('mysql');
   const connection = mysql.createConnection({
       host: 'localhost',
       user: 'root',
       password: '',
       database: 'SGS'
   });
   ```

### Running the Application
1. Start **MySQL** and ensure the database is running.
2. Navigate to the project directory and install dependencies:
   ```sh
   npm install
   ```
3. Start the backend server:
   ```sh
   node server.js
   ```
4. Open the application in your browser at:
   ```
   http://localhost:3000
   ```

---

## 🔮 Future Enhancements
- **Blockchain Integration:** Securely manage data transactions and records.
- **AI-Based Anomaly Detection:** Identify unusual grading patterns.
- **Mobile Application:** Provide access on-the-go for students and faculty.
- **Geolocation-Based Alerts:** Secure access based on device locations.

---




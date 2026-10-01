# Tuition & Class Management System (PHP OOP / MVC / PDO + Tailwind CSS)

A production-ready, modular, and scalable **Tuition & Class Management System** built with native PHP (Object-Oriented Architecture, Model-View-Controller design pattern, and PDO database abstraction), styled with Tailwind CSS, and powered by Vanilla JavaScript.

---

## 🌟 Key Features

### 🔐 1. Authentication & Role-Based Access Control (RBAC)
- Multi-role architecture: **Admin**, **Teacher**, and **Student**.
- CSRF token protection and secure session management.
- Password hashing with PHP `password_hash()` (BCRYPT).

### 🎓 2. Student Directory & Barcode Generator
- Complete student CRUD with unique auto-generated Student Barcode IDs (e.g. `STU-2026-001`).
- Interactive SVG Barcode preview modal ready for ID card printing.
- Multi-course class enrollment management.

### 📱 3. Smart Attendance Scanner
- Real-time USB / Mobile Barcode & QR Code scan processing engine.
- Instant AJAX API integration (`/api/attendance/scan`) with audio beep feedback synthesizers.
- Live scan activity logger table and manual attendance record override.

### 💳 4. Tuition Fee Management & Digital Receipts
- Monthly tuition fee collection and expected revenue calculation.
- Digital printable receipts with unique receipt numbers (`REC-YYYYMM-XXXXX`).
- Student portal for payment history and monthly fee status tracking.

### 📚 5. Study Materials & Past Papers Portal
- Secure PDF file uploads for lecture notes, past papers, and revision sheets.
- Protected storage directory with `.htaccess` direct access prevention.
- Authorized streaming download handler (`/materials/download`).

### ⏱️ 6. Online Quizzes & Live Countdown Timer
- MCQ quiz builder for teachers with custom question text, option sets, and pass mark parameters.
- Live client-side JavaScript countdown timer with progress bar and 1-minute warning alert.
- Auto-submission when time expires and instant automated grading engine.

---

## 🛠️ Technology Stack
- **Backend:** PHP 8+ (OOP, MVC Pattern, PDO Abstraction)
- **Database:** MySQL relational database
- **Frontend:** Tailwind CSS (via CDN), Google Fonts (Inter & Outfit), FontAwesome 6
- **Scripts:** Vanilla JavaScript (Web Audio API synth, fetch API, LocalStorage recovery)

---

## 🚀 Quick Setup & Installation

### 1. Database Setup
1. Start **Apache** and **MySQL** services in XAMPP.
2. Open phpMyAdmin or MySQL terminal and run the schema script located at:
   ```bash
   database/schema.sql
   ```
   Or run via command line:
   ```bash
   mysql -u root class_management_db < database/schema.sql
   ```

### 2. Environment Configuration
Copy `.env.example` to `.env` if not already created:
```env
APP_NAME="Tuition & Class Management System"
APP_ENV=development
APP_URL=http://localhost/myubject/class-management-system/public

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=class_management_db
DB_USER=root
DB_PASS=
```

### 3. Open in Browser
Access the system in your web browser:
- `http://localhost/myubject/class-management-system/public/`
- Or simply: `http://localhost/myubject/`

---

## 🔑 Default Test Credentials

All accounts use default password: **`password123`**

| Role | Email Address | Description |
| :--- | :--- | :--- |
| **Admin** | `admin@tuition.com` | Full system control, student directory, fee ledger, attendance scanner |
| **Teacher** | `john.doe@tuition.com` | Class materials upload, MCQ quiz builder & attendance |
| **Student** | `alex.smith@student.com` | Access study notes, online timed quizzes, view fee receipts |

---

## 📁 Directory Structure

```text
class-management-system/
├── config/
│   ├── database.php             # Secure PDO connection with error handling
│   └── app.php                  # Global constants, session config, app URLs
├── database/
│   └── schema.sql               # Full relational MySQL schema with sample seed data
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php          # Login, Register, Logout, Session check
│   │   ├── StudentController.php       # Student CRUD, Barcode generation logic
│   │   ├── AttendanceController.php    # QR/Barcode scan processing & status logs
│   │   ├── FeeController.php           # Monthly fee payments, receipts, dues
│   │   ├── MaterialController.php      # PDF & past paper uploads, download authorization
│   │   └── QuizController.php          # Quiz CRUD, live countdown taker, auto-grading
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Course.php
│   │   ├── Attendance.php
│   │   ├── Payment.php
│   │   ├── Material.php
│   │   └── Quiz.php
│   └── Middleware/
│       ├── AuthMiddleware.php          # Redirect unauthenticated requests
│       └── RoleMiddleware.php          # Role access control (admin, teacher, student)
├── views/
│   ├── layouts/
│   │   ├── header.php                  # Tailwind CDN, Meta tags, Navbar
│   │   ├── footer.php                  # Scripts, closing tags
│   │   └── sidebar.php                 # Dynamic role-based navigation links
│   ├── auth/
│   │   └── login.php                   # Clean Tailwind login form
│   ├── admin/
│   │   ├── dashboard.php               # Overview stats (fees, attendance, student count)
│   │   └── students.php                # Student management table & registration modal
│   ├── teacher/
│   │   ├── materials.php               # Upload Past Papers, PDF notes
│   │   └── quizzes.php                 # Create quizzes and add MCQ questions
│   └── student/
│       ├── materials.php               # View/Download permitted PDFs
│       ├── take_quiz.php               # Quiz interface with JavaScript countdown timer
│       └── fees.php                    # Payment history & monthly status
├── storage/
│   ├── uploads/
│   │   └── materials/                  # Protected directory for PDF files (.htaccess secured)
│   └── logs/
├── public/
│   ├── index.php                       # Single entry point / Front Controller routing
│   ├── .htaccess                       # URL rewriting to public/index.php
│   └── assets/
│       ├── js/
│       │   ├── scanner.js              # Barcode / QR scanner handler
│       │   └── quiz-timer.js           # Client-side countdown & auto-submission
│       └── css/
├── .env.example
└── README.md
```

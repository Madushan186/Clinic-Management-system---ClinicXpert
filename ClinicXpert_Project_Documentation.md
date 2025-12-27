# ClinicXpert - Complete Project Documentation & Source Code

## 1. Project Overview
**ClinicXpert** is a modern Clinic Management System built with **PHP (Native)**, **MySQL**, **HTML5**, **CSS3**, and **JavaScript**. It features a responsive design with a premium glassmorphism UI.

### Key Features
- **Role-Based Access Control**: Admin, Doctor, and Patient panels.
- **Authentication**: Secure registration and login with password hashing.
- **Appointment Booking**: Patients can check availability and book slots.
- **Admin Dashboard**: Visual analytics (Charts.js) and management of users.
- **Doctor Dashboard**: Daily schedule and patient medical records.
- **Patient Dashboard**: History tracking and upcoming appointments.

### Directory Structure
```
ClinicXpert/
├── admin/          # Admin module files
├── assets/         # CSS and JS files
├── config/         # Database configuration
├── doctor/         # Doctor module files
├── includes/       # Reusable PHP components (header, footer)
├── patient/        # Patient module files
├── sql/            # Database setup script
├── index.php       # Landing page
├── login.php       # Login page
├── register.php    # Registration page
└── logout.php      # Logout logic
```

---

## 2. Database Setup
The database schema defines users, roles, and relationships. Run this SQL script to initialize the database.

### File: `sql/setup.sql`
**Purpose**: Creates tables (`users`, `doctors`, `appointments`, etc.) and inserts seed (test) data.

```sql
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `medical_history`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `schedules`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `users`;

-- Users Table (Stores login info for all roles)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'doctor', 'patient') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors Table
CREATE TABLE `doctors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `specialization` VARCHAR(100) NOT NULL,
  `bio` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Patients Table
CREATE TABLE `patients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Schedules Table (Doctor availability)
CREATE TABLE `schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `doctor_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
);

-- Appointments Table
CREATE TABLE `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` TIME NOT NULL,
  `reason` TEXT,
  `status` ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
);

-- Medical History Table
CREATE TABLE `medical_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT,
  `visit_date` DATE NOT NULL,
  `diagnosis` TEXT NOT NULL,
  `treatment` TEXT NOT NULL,
  `notes` TEXT,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL
);

SET FOREIGN_KEY_CHECKS = 1;

-- Seed Data: Admin (Password: password)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin User', 'admin@clinicxpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
```

---

## 3. Configuration & Common Files

### File: `config/db.php`
**Purpose**: Connects to the database using PDO and defines global constants like `BASE_URL`.

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinicxpert');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/ClinicXpert'); // IMPORTANT: Change this if deploying to root

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
```

### File: `includes/functions.php`
**Purpose**: Helper functions for sessions, authentication, and security.

```php
<?php
session_start();

function isLoggedIn() { return isset($_SESSION['user_id']); }

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        die("Access Denied.");
    }
}

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function formatDate($date) { return date("M d, Y", strtotime($date)); }
function formatTime($time) { return date("h:i A", strtotime($time)); }

function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
    }
    return '';
}
```

### File: `includes/header.php`
**Purpose**: HTML Header containing CSS links, Fonts, and Navbar.

```php
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>ClinicXpert</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="<?php echo BASE_URL; ?>/" class="logo">
                <i class="fas fa-heartbeat"></i> ClinicXpert
            </a>
            <nav>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Dynamic Role Links -->
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li><a href="<?php echo BASE_URL; ?>/admin/index.php">Dashboard</a></li>
                         <?php elseif ($_SESSION['role'] == 'doctor'): ?>
                            <li><a href="<?php echo BASE_URL; ?>/doctor/index.php">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>/patient/index.php">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-outline btn-sm">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary btn-sm">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <?php if (function_exists('getFlash')) echo getFlash(); ?>
```

### File: `includes/footer.php`
**Purpose**: Closes the HTML document and includes scripts.

```php
    </main>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> ClinicXpert. All rights reserved.</p>
        </div>
    </footer>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
```

---

## 4. Authentication Module

### File: `login.php`
**Purpose**: Authenticates users and redirects them based on role.

**Key Logic**:
1. Checks if email exists in `users` table.
2. Verifies password using `password_verify()`.
3. Sets `$_SESSION` variables.
4. Redirects: Admin -> `/admin`, Doctor -> `/doctor`, Patient -> `/patient`.

### File: `register.php`
**Purpose**: Registers new users. Supports "Patient" and "Doctor" roles.

**Key Logic**:
1. Accepts name, email, password, role.
2. If Patient: Asks for DOB, Gender, Phone.
3. If Doctor: Creates placeholder doctor profile.
4. Uses Transactions (`$pdo->beginTransaction()`) to ensure both `users` table and role-specific table (`patients` or `doctors`) are updated atomically.

### File: `logout.php`
**Purpose**: Destroys session and redirects to login.

```php
<?php
require_once 'config/db.php';
session_start();
session_destroy();
header("Location: " . BASE_URL . "/login.php");
exit;
```

---

## 5. Admin Module (`/admin`)

### File: `admin/index.php` (Dashboard)
**Purpose**: Overview of clinic stats using **Chart.js**.
**Key Features**:
- Cards for Total Doctors, Patients, Appointments.
- Graphs for Appointment status breakdown.
- Table of Recent appointments.

### File: `admin/doctors.php`
**Purpose**: Add and view doctors.
**Key Logic**:
- Form to add new Doctor (creates User + Doctor entry + default Schedule).
- List of all doctors.

### File: `admin/appointments.php`
**Purpose**: Manage all appointments.
**Key Logic**:
- Lists all appointments with status.
- Actions: Confirm or Cancel pending appointments.

---

## 6. Doctor Module (`/doctor`)

### File: `doctor/index.php` (Dashboard)
**Purpose**: Today's overview.
**Key Features**:
- Shows appointments scheduled for **today only**.
- Shows count of pending requests.

### File: `doctor/schedule.php`
**Purpose**: Manage availability.
**Key Logic**:
- Interface to set Start/End time for each day of the week.
- Updates `schedules` table.

### File: `doctor/appointments.php`
**Purpose**: Complete appointments.
**Key Logic**:
- view appointments.
- "Complete" action opens a form to enter **Diagnosis, Treatment, Notes**.
- Submitting saves to `medical_history` table and updates status to 'Completed'.

---

## 7. Patient Module (`/patient`)

### File: `patient/book.php`
**Purpose**: Book new appointments.
**Key Logic**:
- Select Doctor -> Date -> Time.
- **Validation**:
    1. Checks if Doctor works on that day (`schedules` table).
    2. Checks if Doctor is already booked at that time.
- Inserts into `appointments` with status 'Pending'.

### File: `patient/history.php`
**Purpose**: View medical records.
**Key Logic**:
- Fetches from `medical_history` table joined with doctors.
- Displays previous diagnoses and treatments.

---

## 8. Development Flow & Logic

### How the System Works
1.  **Initialization**:
    - The `index.php` is the landing page.
    - `config/db.php` sets up the database connection.
    - `includes/functions.php` provides global tools like `BASE_URL` handling.

2.  **Navigation**:
    - The `header.php` dynamically renders links based on `$_SESSION['role']`.
    - If not logged in, it shows Login/Signup.

3.  **Booking Logic**:
    - Patient selects a doctor.
    - System checks `schedules` to see valid work days.
    - System checks `appointments` to prevent double-booking.
    - If valid, a 'Pending' appointment is created.

4.  **Appointment Lifecycle**:
    - **Pending**: Created by Patient.
    - **Confirmed**: Admin or Doctor acknowledges it.
    - **Completed**: Doctor finishes the visit and adds medical notes.
    - **Cancelled**: Admin or Patient cancels it.

5.  **Security Measures**:
    - **SQL Injection**: Prevented using PDO Prepared Statements (`$stmt->prepare()`).
    - **XSS**: Prevented using `cleanInput()` which runs `htmlspecialchars()`.
    - **Password Storage**: Secure bcrypt hashing via `password_hash()`.
    - **Access Control**: `requireRole('admin')` ensures unauthorized users cannot access protected pages.

### How to Recreate
1.  **Install XAMPP** (or any LAMP stack).
2.  **Database**: Create a database named `clinicxpert`. Import `sql/setup.sql`.
3.  **Files**: Copy the folder structure and files exactly as listed above.
4.  **Config**: Open `config/db.php` and verify `DB_USER` and `DB_PASS`.
5.  **Run**: Open browser to `http://localhost/ClinicXpert/`.

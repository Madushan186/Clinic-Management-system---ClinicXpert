🏥 ClinicXpert – Clinic Management System

ClinicXpert is a full-featured Clinic Management System built using Native PHP, MySQL, HTML, CSS, and JavaScript.
It provides a complete digital solution for managing doctors, patients, appointments, and medical records with secure role-based access.

Designed with a modern UI (glassmorphism) and clean backend architecture, ClinicXpert demonstrates real-world web application development practices.

🚀 Live Features
🔐 Authentication & Security

Secure Login & Registration
Password hashing using password_hash()
Role-based access control (Admin / Doctor / Patient)
Session-based authentication

👨‍⚕️ Admin Panel

Dashboard analytics with Chart.js
Manage doctors and patients
View & manage all appointments
Confirm or cancel bookings

🩺 Doctor Panel

Daily appointment overview
Manage weekly schedules
Complete appointments
Add diagnosis, treatment & medical notes

👤 Patient Panel

Book appointments based on doctor availability
Prevent double booking
View appointment history
Access personal medical records

🧱 Tech Stack
Layer	Technology
Backend	PHP (Native)
Database	MySQL
Frontend	HTML5, CSS3, JavaScript
Charts	Chart.js
Security	PDO, Prepared Statements
Server	Apache (XAMPP / LAMP)

📂 Project Structure
ClinicXpert/
├── admin/          # Admin module
├── doctor/         # Doctor module
├── patient/        # Patient module
├── assets/         # CSS & JS files
├── config/         # Database configuration
├── includes/       # Header, footer, helper functions
├── sql/            # Database setup script
├── index.php       # Landing page
├── login.php       # Login
├── register.php    # Registration
└── logout.php      # Logout

🗄️ Database Design

Key tables:

users – Authentication & roles
doctors – Doctor profiles
patients – Patient details
schedules – Doctor availability
appointments – Appointment lifecycle
medical_history – Diagnosis & treatment records

✔ Fully normalized
✔ Foreign key constraints
✔ Cascading deletes

🔄 Appointment Workflow

Patient selects doctor, date & time
System validates:
Doctor availability
Time slot availability
Appointment created as Pending
Admin/Doctor confirms
Doctor completes appointment
Medical history saved permanently

🔒 Security Practices

✅ PDO Prepared Statements (SQL Injection protection)
✅ Input sanitization (htmlspecialchars)
✅ Password hashing (bcrypt)
✅ Role-based page restrictions
✅ Session protection

⚙️ Installation Guide
1️⃣ Clone Repository
git clone https://github.com/your-username/ClinicXpert.git

2️⃣ Setup Database

Create database: clinicxpert

Import:
sql/setup.sql

3️⃣ Configure Database

Edit:

config/db.php

define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/ClinicXpert');

4️⃣ Run Application

Open in browser:

http://localhost/ClinicXpert/

🔑 Default Admin Login
Email: admin@clinicxpert.com
Password: password

🎯 Learning Outcomes

PHP MVC-style structuring (without frameworks)

Secure authentication handling

Real-world appointment systems

Database relationship design

Clean UI + backend integration

🛠️ Future Improvements

Email notifications
Appointment reminders
Payment gateway integration
REST API support
AJAX-based booking
Mobile responsive improvements

👨‍💻 Author

Lakshitha Madushan
UX/UI Designer | Full-Stack Developer | Engineering Undergraduate

GitHub: https://github.com/Madushan186


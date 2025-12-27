-- Database Schema for ClinicXpert

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist
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

-- Seed Data

-- Admin
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin User', 'admin@clinicxpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- password is 'password'

-- Doctors
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Dr. Kamal Perera', 'kamal@clinicxpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor'),
('Dr. Nimsara Jayasinghe', 'nimsara@clinicxpert.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor');

INSERT INTO `doctors` (`user_id`, `specialization`, `bio`) VALUES
(2, 'Cardiology', 'Expert in heart health with 10 years of experience at National Hospital.'),
(3, 'Pediatrics', 'Specialist in child care and development, formerly at Lady Ridgeway.');

-- Schedules
INSERT INTO `schedules` (`doctor_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 'Monday', '09:00:00', '17:00:00'),
(1, 'Wednesday', '09:00:00', '13:00:00'),
(2, 'Tuesday', '10:00:00', '18:00:00');

-- Patients
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Kasun Bandara', 'kasun@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient'),
('Thilini Silva', 'thilini@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient');

INSERT INTO `patients` (`user_id`, `dob`, `gender`, `phone`, `address`) VALUES
(4, '1985-06-15', 'Male', '077-1234567', '123 Galle Road, Colombo 03'),
(5, '1992-09-23', 'Female', '071-9876543', '45/B Kandy Road, Gampaha');

-- Appointments
INSERT INTO `appointments` (`patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `reason`, `status`) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 'Annual checkup (Payment: 2500 LKR)', 'Pending'),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:30:00', 'Child fever (Payment: 1500 LKR)', 'Confirmed');


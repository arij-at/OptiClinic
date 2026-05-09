-- OptiClinic Database Schema
-- Run this SQL script to create the database and tables

CREATE DATABASE IF NOT EXISTS opticlinic;
USE opticlinic;

-- 1. Users (staff only — patients are NOT users)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),       -- hashed
  phone VARCHAR(20),
  role ENUM('receptionist','doctor')
);

-- 2. Patients (created per form submission, no login)
CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  date_of_birth DATE,
  email VARCHAR(100),
  phone VARCHAR(20),
  token VARCHAR(64) UNIQUE,    -- random token for appointment access URL
  created_at DATETIME DEFAULT NOW()
);

-- 3. Intake forms
CREATE TABLE intake_forms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT,
  symptoms_description TEXT,   -- free text field
  q_which_eye VARCHAR(10),     -- left, right, both
  q_how_long VARCHAR(20),      -- today, few_days, week, more
  q_pain_level TINYINT,        -- 0-4 points
  q_redness TINYINT,           -- 0-4 points
  q_discharge TINYINT,         -- 0-4 points
  q_vision TINYINT,            -- 0-4 points
  triage_score INT,            -- computed total
  priority ENUM('P3','P2','P1') DEFAULT 'P3',
  is_ambiguous BOOLEAN DEFAULT 0,
  routing ENUM('auto','receptionist'),
  submitted_at DATETIME DEFAULT NOW(),
  FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- 4. Time slots
CREATE TABLE time_slots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE,
  start_time TIME,
  end_time TIME,
  is_available BOOLEAN DEFAULT 1
);

-- 5. Appointments
CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT,
  form_id INT,
  slot_id INT,
  status ENUM('pending','confirmed','modified','cancelled') DEFAULT 'pending',
  priority ENUM('P1','P2','P3'),
  confirmation_sent_at DATETIME,
  created_at DATETIME DEFAULT NOW(),
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  FOREIGN KEY (form_id) REFERENCES intake_forms(id),
  FOREIGN KEY (slot_id) REFERENCES time_slots(id)
);

-- 6. Notifications (email log)
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT,
  type ENUM('confirmation','apology','info'),
  status ENUM('pending','sent','failed') DEFAULT 'pending',
  sent_at DATETIME,
  FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- 7. Doctor change requests & comments
CREATE TABLE slot_flags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT,
  flagged_by INT,              -- doctor user id
  comment TEXT,
  resolved BOOLEAN DEFAULT 0,
  created_at DATETIME DEFAULT NOW(),
  FOREIGN KEY (appointment_id) REFERENCES appointments(id),
  FOREIGN KEY (flagged_by) REFERENCES users(id)
);

-- Insert sample users (passwords hashed with password_hash())
-- Receptionist: email: rec@clinic.com, password: rec123
INSERT INTO users (name, email, password, phone, role) VALUES
('Receptionist', 'rec@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456789', 'receptionist'),
('Doctor', 'doc@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654321', 'doctor');
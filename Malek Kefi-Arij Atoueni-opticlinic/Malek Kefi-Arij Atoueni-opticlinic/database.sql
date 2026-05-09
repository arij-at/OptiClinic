-- Database schema for OptiClinic

CREATE DATABASE IF NOT EXISTS opticlinic;
USE opticlinic;

-- Patients table
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Intake forms table
CREATE TABLE intake_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    symptoms_description TEXT,
    q_which_eye VARCHAR(10),
    q_how_long VARCHAR(20),
    q_pain_level TINYINT NOT NULL CHECK (q_pain_level BETWEEN 0 AND 4),
    q_redness TINYINT NOT NULL CHECK (q_redness BETWEEN 0 AND 4),
    q_discharge TINYINT NOT NULL CHECK (q_discharge BETWEEN 0 AND 4),
    q_vision TINYINT NOT NULL CHECK (q_vision BETWEEN 0 AND 4),
    triage_score INT NOT NULL,
    priority ENUM('P1', 'P2', 'P3') NOT NULL,
    is_ambiguous BOOLEAN DEFAULT 0,
    routing ENUM('auto', 'receptionist') NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Time slots table
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT 1,
    INDEX idx_date_available (date, is_available)
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    form_id INT NOT NULL,
    slot_id INT NULL,
    status ENUM('pending', 'confirmed', 'modified') DEFAULT 'pending',
    priority ENUM('P1', 'P2', 'P3') NOT NULL,
    preferred_slot_id INT NULL,
    confirmation_sent_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (form_id) REFERENCES intake_forms(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES time_slots(id) ON DELETE SET NULL,
    FOREIGN KEY (preferred_slot_id) REFERENCES time_slots(id) ON DELETE SET NULL
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    type ENUM('confirmation', 'apology', 'reminder') NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at DATETIME NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('receptionist', 'doctor') NOT NULL
);

-- Slot flags table
CREATE TABLE slot_flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    flagged_by INT NOT NULL,
    comment TEXT NOT NULL,
    resolved BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (flagged_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Alter appointments table
ALTER TABLE appointments
ADD COLUMN assigned_by ENUM('auto', 'receptionist') DEFAULT NULL,
ADD COLUMN assigned_at DATETIME DEFAULT NULL;

-- Alter notifications table
ALTER TABLE notifications
ADD COLUMN appointment_id INT NULL,
ADD COLUMN subject VARCHAR(255),
ADD COLUMN message TEXT,
ADD FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL;

-- Appointment changes table
CREATE TABLE appointment_changes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    changed_by INT NOT NULL,
    old_slot_id INT,
    new_slot_id INT,
    reason TEXT,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE
);
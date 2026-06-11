-- Digital School Report Card Management System Schema

-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS school_report_db;
USE school_report_db;

-- 1. Schools Table (Multi-school support)
CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(255) NOT NULL,
    school_address TEXT,
    school_phone VARCHAR(20),
    school_email VARCHAR(100),
    school_badge VARCHAR(255), -- Path to badge image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Users Table (RBAC)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('system_admin', 'school_admin', 'bursar', 'teacher', 'student') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- 3. Students Table (Detailed student info)
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    school_id INT,
    student_id_number VARCHAR(50) NOT NULL UNIQUE,
    class_name VARCHAR(50) NOT NULL,
    stream VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    guardian_name VARCHAR(100),
    guardian_phone VARCHAR(20),
    guardian_place_of_work VARCHAR(255),
    guardian_profession VARCHAR(100),
    student_age INT,
    nationality VARCHAR(50),
    student_status VARCHAR(100), -- chronic, orphan, etc.
    lin_number VARCHAR(50),
    report_blocked BOOLEAN DEFAULT FALSE,
    block_reason TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- 4. Academic Periods (Years and Terms)
CREATE TABLE IF NOT EXISTS academic_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT,
    year YEAR NOT NULL,
    term VARCHAR(50) NOT NULL, -- e.g., Term 1, Term 2, Term 3
    start_date DATE,
    end_date DATE,
    is_active BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- 5. Subjects
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT,
    subject_code VARCHAR(20),
    subject_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- 6. Marks/Academic Records
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    period_id INT,
    teacher_id INT,
    mid_term_mark DECIMAL(5,2),
    end_term_mark DECIMAL(5,2),
    total_mark DECIMAL(5,2),
    grade VARCHAR(5),
    teacher_remark TEXT,
    is_locked BOOLEAN DEFAULT FALSE, -- Prevent changes after approval
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES academic_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 7. Fees Records
CREATE TABLE IF NOT EXISTS fees_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    period_id INT,
    total_payable DECIMAL(10,2),
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    balance DECIMAL(10,2) GENERATED ALWAYS AS (total_payable - amount_paid) STORED,
    status ENUM('paid', 'partial', 'unpaid') DEFAULT 'unpaid',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES academic_periods(id) ON DELETE CASCADE
);

-- 8. Discipline Records
CREATE TABLE IF NOT EXISTS discipline_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    period_id INT,
    infraction TEXT,
    action_taken TEXT,
    is_blocking_report BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES academic_periods(id) ON DELETE CASCADE
);

-- 9. Audit Logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 10. Login Attempts (Rate Limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- SAMPLE DATA

-- Insert Homisdallen Primary School Branches
INSERT INTO schools (school_name, school_address, school_phone, school_email) 
VALUES ('Homisdallen Primary School - Gayaza', 'Gayaza Road, Kampala', '+256 701 111111', 'gayaza@homisdallen.com');

INSERT INTO schools (school_name, school_address, school_phone, school_email) 
VALUES ('Homisdallen Primary School - Kyebando', 'Kyebando Central, Kampala', '+256 702 222222', 'kyebando@homisdallen.com');

INSERT INTO schools (school_name, school_address, school_phone, school_email) 
VALUES ('Homisdallen Primary School - Kamwokya', 'Kamwokya Hill, Kampala', '+256 703 333333', 'kamwokya@homisdallen.com');

-- Insert System Admin
-- Password is 'admin123' hashed with password_hash() in PHP
INSERT INTO users (school_id, username, password, full_name, email, role) 
VALUES (NULL, 'sysadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'sysadmin@system.com', 'system_admin');

-- Insert School Admin for Homisdallen Gayaza
INSERT INTO users (school_id, username, password, full_name, email, role) 
VALUES (1, 'homisdallen_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gayaza Admin', 'admin@homisdallen.com', 'school_admin');

-- Insert a Teacher
INSERT INTO users (school_id, username, password, full_name, email, role) 
VALUES (1, 'teacher_john', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'john.doe@greenhill.edu', 'teacher');

-- Insert a Bursar
INSERT INTO users (school_id, username, password, full_name, email, role) 
VALUES (1, 'bursar_mary', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary Smith', 'mary.smith@greenhill.edu', 'bursar');

-- Insert a Student User
INSERT INTO users (school_id, username, password, full_name, email, role) 
VALUES (1, 'std001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice Johnson', 'alice@student.com', 'student');

-- Insert Student Detail
INSERT INTO students (user_id, school_id, student_id_number, class_name, stream, gender) 
VALUES (5, 1, 'GH-2026-001', 'Primary 5', 'Blue', 'female');

-- Insert Academic Period
INSERT INTO academic_periods (school_id, year, term, is_active) 
VALUES (1, 2026, 'Term 1', TRUE);

-- Insert Subjects
INSERT INTO subjects (school_id, subject_code, subject_name) VALUES (1, 'MAT', 'Mathematics');
INSERT INTO subjects (school_id, subject_code, subject_name) VALUES (1, 'ENG', 'English');
INSERT INTO subjects (school_id, subject_code, subject_name) VALUES (1, 'SCI', 'Science');

-- Insert Sample Fees for the student
INSERT INTO fees_status (student_id, period_id, total_payable, amount_paid, status) 
VALUES (1, 1, 500000, 200000, 'partial');

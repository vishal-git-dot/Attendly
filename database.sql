CREATE DATABASE IF NOT EXISTS qr_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qr_attendance;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher','student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    course VARCHAR(120) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE attendance_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NOT NULL,
    subject VARCHAR(160) NOT NULL,
    session_token CHAR(48) NOT NULL UNIQUE,
    session_date DATE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_session_date (session_date)
);

CREATE TABLE attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    marked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_session (session_id, student_id),
    INDEX idx_student_attendance (student_id)
);

-- Demo password for both accounts: password
INSERT INTO users (name,email,password,role) VALUES
('Dr. Ananya Menon','teacher@attendly.test','$2y$12$e5Cjq0HG.yzNQCX0u.HO2OkXvs6281w1KKmspNwXzwcm3GdX.afre','teacher'),
('Doe Student','student@attendly.test','$2y$12$e5Cjq0HG.yzNQCX0u.HO2OkXvs6281w1KKmspNwXzwcm3GdX.afre','student');

INSERT INTO students (user_id,roll_number,course) VALUES
(2,'CS2026-001','MCA');

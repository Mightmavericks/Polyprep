-- Polyprep Database Schema

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS polyprep;
CREATE DATABASE polyprep CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE polyprep;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Semesters table
CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_semester (course_id, name)
) ENGINE=InnoDB;

-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject (semester_id, name)
) ENGINE=InnoDB;

-- Notes table
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    is_approved BOOLEAN NOT NULL DEFAULT 0,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert admin user (password: admin123)
INSERT INTO users (username, password_hash, is_admin) 
VALUES ('admin', '$2y$10$uqbIXpUWQVl9xH2L8YQE9.t4WS9wS6S7TDFJtJ3rxLXLvQGWYNBpK', 1);

-- Insert sample courses
INSERT INTO courses (name) VALUES 
('Computer Science'),
('Electrical Engineering'),
('Mechanical Engineering'),
('Civil Engineering');

-- Insert sample semesters for Computer Science
INSERT INTO semesters (course_id, name) VALUES 
(1, 'Semester 1'),
(1, 'Semester 2'),
(1, 'Semester 3'),
(1, 'Semester 4');

-- Insert sample subjects for Computer Science Semester 1
INSERT INTO subjects (semester_id, name) VALUES 
(1, 'Introduction to Programming'),
(1, 'Discrete Mathematics'),
(1, 'Digital Logic Design'),
(1, 'Calculus');

-- Insert sample subjects for Computer Science Semester 2
INSERT INTO subjects (semester_id, name) VALUES 
(2, 'Data Structures'),
(2, 'Object-Oriented Programming'),
(2, 'Computer Organization'),
(2, 'Linear Algebra');

-- Create indexes for better performance
CREATE INDEX idx_notes_approval ON notes(is_approved);
CREATE INDEX idx_notes_user ON notes(user_id);
CREATE INDEX idx_notes_subject ON notes(subject_id);

-- MySQL Database Initialization for Eduloom Education
-- This file will be executed when the MySQL container starts

USE eduloom_education_db;

-- Create enquiries table
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    program VARCHAR(100) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- Insert sample data (optional)
INSERT INTO enquiries (name, email, phone, program, message) VALUES
('John Doe', 'john.doe@example.com', '+1-555-0123', 'undergraduate', 'I am interested in computer science programs.'),
('Jane Smith', 'jane.smith@example.com', '+1-555-0124', 'graduate', 'Looking for MBA programs with flexible schedules.');

-- Create users table for future authentication (optional)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'counselor', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create universities table for future expansion
CREATE TABLE IF NOT EXISTS universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    ranking INT,
    website VARCHAR(255),
    description TEXT,
    logo_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample universities data
INSERT INTO universities (name, country, ranking, website, description) VALUES
('Harvard University', 'USA', 1, 'https://www.harvard.edu', 'Prestigious Ivy League university known for excellence in education and research.'),
('Oxford University', 'United Kingdom', 2, 'https://www.ox.ac.uk', 'One of the oldest and most prestigious universities in the world.'),
('MIT', 'USA', 3, 'https://www.mit.edu', 'Leading institution for technology, engineering, and scientific research.'),
('Stanford University', 'USA', 4, 'https://www.stanford.edu', 'Premier research university in Silicon Valley.'),
('Cambridge University', 'United Kingdom', 5, 'https://www.cam.ac.uk', 'Historic university renowned for academic excellence.'),
('University of Toronto', 'Canada', 6, 'https://www.utoronto.ca', 'Top Canadian university with strong international reputation.'),
('Australian National University', 'Australia', 7, 'https://www.anu.edu.au', 'Leading research university in Australia.'),
('University of Melbourne', 'Australia', 8, 'https://www.unimelb.edu.au', 'Prestigious Australian university with global recognition.');

-- Create programs table for future expansion
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT,
    name VARCHAR(255) NOT NULL,
    degree_type ENUM('undergraduate', 'graduate', 'language', 'certificate') NOT NULL,
    duration_months INT,
    tuition_fee DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'USD',
    description TEXT,
    requirements TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
);

-- Grant permissions to the application user
GRANT SELECT, INSERT, UPDATE, DELETE ON eduloom_education_db.* TO 'eduloom_education_user'@'%';
FLUSH PRIVILEGES;

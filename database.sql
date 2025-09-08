CREATE DATABASE student_accommodation;
USE student_accommodation;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    gender ENUM('male', 'female', 'other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE residences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    available_slots INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    residence_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (residence_id) REFERENCES residences(id)
);

CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE application_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    residence_id INT NOT NULL,
    preference_rank INT NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (residence_id) REFERENCES residences(id)
);

ALTER TABLE students
ADD COLUMN is_active TINYINT(1) DEFAULT 1;

CREATE TABLE residence_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    residence_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (residence_id) REFERENCES residences(id) ON DELETE CASCADE
);

-- Insert a sample admin user
INSERT INTO admins (username, password, email)
VALUES ('admin', 'admin123', 'admin@example.com');

-- Insert sample residences
INSERT INTO residences (name, description, location, capacity, available_slots)
VALUES
('Sunny Hills Residence', 'Modern residence with Wi-Fi and study rooms', 'Pretoria', 100, 50),
('Green Valley Lodge', 'Affordable accommodation near campus', 'Johannesburg', 80, 30);

--Insert residences images
INSERT INTO residence_images (residence_id, image_path) VALUES
(1, 'images/uploads/1747707534_pexels-jovydas-2462015.jpg'),
(2, 'images/uploads/1747707601_pexels-willian-santos-44398111-32126315.jpg');

UPDATE residence_images
SET image_path = '../images/uploads/1747707534_pexels-jovydas-2462015.jpg'
WHERE residence_id = 1;

UPDATE residence_images
SET image_path = '../images/uploads/1747707601_pexels-willian-santos-44398111-32126315.jpg'
WHERE residence_id = 2;

ALTER TABLE applications
ADD COLUMN comments VARCHAR(255) DEFAULT NULL;

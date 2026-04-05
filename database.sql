-- Create database
CREATE DATABASE IF NOT EXISTS campus_store;
USE campus_store;

-- Create students table
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admin table
CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    product_id INT,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processed', 'Cancelled') DEFAULT 'Pending',
    payment_status ENUM('Paid', 'Pending') DEFAULT 'Pending',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create printing_orders table
CREATE TABLE printing_orders (
    print_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    file_path VARCHAR(255) NOT NULL,
    pages INT NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processed', 'Cancelled') DEFAULT 'Pending',
    payment_status ENUM('Paid', 'Pending') DEFAULT 'Pending',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$YourHashedPasswordHere');
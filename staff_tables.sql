-- Staff Basic Information
CREATE TABLE staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    address TEXT,
    joining_date DATE,
    salary DECIMAL(10,2),
    role ENUM('washer', 'ironer', 'packer', 'delivery') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Staff Attendance
CREATE TABLE staff_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT,
    date DATE,
    status ENUM('present', 'absent', 'half-day', 'leave'),
    in_time TIME,
    out_time TIME,
    FOREIGN KEY (staff_id) REFERENCES staff(id)
);

-- Staff Tasks
CREATE TABLE staff_tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT,
    order_id INT,
    task_type ENUM('washing', 'ironing', 'packing', 'delivery'),
    status ENUM('pending', 'in_progress', 'completed'),
    assigned_date DATETIME,
    completion_date DATETIME,
    FOREIGN KEY (staff_id) REFERENCES staff(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
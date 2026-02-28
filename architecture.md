## 🗄️ Database Configuration
- **DB Name:** `sports_borrow_db`
- **User:** `dev_user`
- **Password:** `dev_password`
- **Host:** `db` (Docker Service Name)

## 🛠️ SQL Script (Initial Table)
```sql
CREATE DATABASE IF NOT EXISTS sports_borrow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sports_borrow_db;

-- ตารางผู้ใช้งาน (สำหรับ Login)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- เพิ่ม User ทดสอบ (Password: 123456)
INSERT INTO users (username, password, fullname, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');


-- ตารางอุปกรณ์กีฬา
CREATE TABLE equipments (
    eq_id INT AUTO_INCREMENT PRIMARY KEY,
    eq_name VARCHAR(100) NOT NULL,
    eq_type VARCHAR(50),
    total_qty INT DEFAULT 0,
    remain_qty INT DEFAULT 0,
    eq_image VARCHAR(255),
    status ENUM('available', 'maintenance', 'out_of_stock') DEFAULT 'available'
) ENGINE=InnoDB;

-- ตารางการยืม-คืน
CREATE TABLE transactions (
    trans_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    eq_id INT,
    borrow_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    return_date DATETIME,
    status ENUM('borrowing', 'returned', 'late') DEFAULT 'borrowing',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (eq_id) REFERENCES equipments(eq_id)
) ENGINE=InnoDB;
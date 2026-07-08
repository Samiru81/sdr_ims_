DROP DATABASE IF EXISTS sdr_ims;
CREATE DATABASE sdr_ims CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE sdr_ims;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','staff') DEFAULT 'staff',
    status ENUM('active','inactive') DEFAULT 'active',
    last_login DATETIME NULL,
    profile_picture VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(20) DEFAULT '#4f8ef7',
    icon VARCHAR(50) DEFAULT 'fa-box'
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(120),
    phone VARCHAR(10),
    email VARCHAR(120),
    address TEXT
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(10),
    email VARCHAR(120),
    address TEXT
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    barcode VARCHAR(100) NULL UNIQUE,
    product_name VARCHAR(180) NOT NULL,
    category_id INT NULL,
    supplier_id INT NULL,
    unit VARCHAR(40) DEFAULT 'pcs',
    location VARCHAR(80),
    quantity INT DEFAULT 0,
    min_stock INT DEFAULT 10,
    cost_price DECIMAL(12,2) DEFAULT 0,
    selling_price DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    movement_type ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
    quantity INT NOT NULL,
    before_qty INT NOT NULL,
    after_qty INT NOT NULL,
    reference_no VARCHAR(80),
    note TEXT,
    created_by VARCHAR(120),
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    supplier_id INT NULL,
    quantity INT NOT NULL,
    cost_price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    reference_no VARCHAR(80),
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    customer_id INT NULL,
    quantity INT NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    reference_no VARCHAR(80),
    issued_by VARCHAR(120),
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

INSERT INTO users(full_name, username, email, password, role, status) VALUES
('System Admin', 'admin', 'admin@ims.lk', '$2y$12$.BYs/t1dIaVR5Bkg1UXfQOEebZUny3oPg3XnI5Fv07ri55keYlh7e', 'admin', 'active'),
('Inventory Manager', 'manager', 'manager@ims.lk', '$2y$12$.BYs/t1dIaVR5Bkg1UXfQOEebZUny3oPg3XnI5Fv07ri55keYlh7e', 'manager', 'active'),
('John Staff', 'staff1', 'staff@ims.lk', '$2y$12$.BYs/t1dIaVR5Bkg1UXfQOEebZUny3oPg3XnI5Fv07ri55keYlh7e', 'staff', 'active');

INSERT INTO categories(category_name, description, color, icon) VALUES
('Electronics', 'Electronic devices and accessories', '#6366f1', 'fa-laptop'),
('Office Supplies', 'Stationery and office materials', '#0ea5e9', 'fa-clipboard'),
('Furniture', 'Office furniture items', '#f59e0b', 'fa-chair'),
('Chemicals', 'Cleaning and chemical items', '#ef4444', 'fa-flask'),
('Tools & Equipment', 'Maintenance tools and equipment', '#10b981', 'fa-screwdriver-wrench'),
('Packaging', 'Boxes, bags and wrapping materials', '#8b5cf6', 'fa-box-open');

INSERT INTO suppliers(supplier_name, contact_person, phone, email, address) VALUES
('TechSource Lanka', 'Kamal Perera', '0112345678', 'kamal@techsource.lk', 'Colombo'),
('Office World PVT', 'Nimal Silva', '0114567890', 'nimal@officeworld.lk', 'Kandy'),
('Furniture Hub', 'Sunil Fernando', '0117654321', 'sunil@furnitures.lk', 'Galle'),
('CleanChem Distributors', 'Amali Dias', '0119876543', 'amali@cleanchem.lk', 'Colombo'),
('Tool Masters', 'Roshan Wijesinghe', '0113456789', 'roshan@toolmasters.lk', 'Negombo');

INSERT INTO customers(customer_name, phone, email, address) VALUES
('Cash Customer', '0000000000', 'cash@example.com', 'Walk-in customer'),
('ABC Company', '0771234567', 'abc@example.com', 'Colombo');

INSERT INTO products(sku, barcode, product_name, category_id, supplier_id, unit, location, quantity, min_stock, cost_price, selling_price) VALUES
('SKU-001', '893000000001', 'Laptop HP EliteBook', 1, 1, 'pcs', 'A1-01', 25, 10, 120000, 145000),
('SKU-002', '893000000002', 'Wireless Mouse Logitech', 1, 1, 'pcs', 'A1-02', 80, 10, 2500, 3500),
('SKU-003', '893000000003', 'A4 Paper Ream', 2, 2, 'ream', 'B2-01', 3, 10, 350, 480),
('SKU-004', '893000000004', 'Ballpoint Pen Box', 2, 2, 'box', 'B2-02', 45, 10, 500, 750),
('SKU-005', '893000000005', 'Executive Office Chair', 3, 3, 'pcs', 'C1-01', 12, 10, 18000, 25000),
('SKU-006', '893000000006', 'Office Desk', 3, 3, 'pcs', 'C1-02', 4, 10, 28000, 38000),
('SKU-007', '893000000007', 'Floor Cleaner 5L', 4, 4, 'can', 'D1-01', 2, 10, 850, 1200),
('SKU-008', '893000000008', 'Screwdriver Set', 5, 5, 'set', 'E1-01', 30, 10, 1600, 2400),
('SKU-009', '893000000009', 'Packing Tape', 6, 5, 'roll', 'F1-01', 250, 10, 120, 220),
('SKU-010', '893000000010', 'Cardboard Box Medium', 6, 5, 'pcs', 'F1-02', 500, 10, 65, 120);

INSERT INTO stock_movements(product_id, movement_type, quantity, before_qty, after_qty, reference_no, note, created_by) VALUES
(1, 'IN', 25, 0, 25, 'PO-2024-001', 'Opening stock', 'System Admin'),
(2, 'IN', 80, 0, 80, 'PO-2024-001', 'Opening stock', 'System Admin'),
(3, 'IN', 50, 0, 50, 'PO-2024-002', 'Opening stock', 'System Admin'),
(3, 'OUT', 47, 50, 3, 'REF-OUT-001', 'Office usage', 'Inventory Manager'),
(7, 'OUT', 18, 20, 2, 'REF-OUT-002', 'Cleaning stock issued', 'Inventory Manager');

-- Ensure every low stock alert uses minimum 10 quantity
UPDATE products SET min_stock = GREATEST(min_stock, 10);

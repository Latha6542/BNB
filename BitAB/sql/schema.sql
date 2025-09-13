-- schema.sql
CREATE DATABASE IF NOT EXISTS fundflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fundflow;

-- users (admins / viewers)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','vendor','viewer') NOT NULL DEFAULT 'viewer',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- institutions
CREATE TABLE institutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  code VARCHAR(64) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- budgets (top-level budgets uploaded by institution)
CREATE TABLE budgets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  institution_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  amount DECIMAL(20,2) NOT NULL DEFAULT 0,
  fiscal_year VARCHAR(20),
  uploaded_by INT,
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- departments (under an institution)
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  institution_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- projects
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  budget_id INT NOT NULL,
  department_id INT,
  name VARCHAR(255) NOT NULL,
  allocated_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- vendors
CREATE TABLE vendors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  contact_info TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- transactions (fund releases/payments)
CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tx_uuid VARCHAR(64) NOT NULL UNIQUE, -- traceable ID
  budget_id INT NOT NULL,
  project_id INT,
  vendor_id INT,
  amount DECIMAL(20,2) NOT NULL,
  status ENUM('initiated','released','received','reconciled','rejected') DEFAULT 'initiated',
  reference VARCHAR(255),
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  metadata JSON DEFAULT NULL,
  FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
  FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- audit logs (immutable trail)
CREATE TABLE audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  entity_type VARCHAR(50) NOT NULL, -- e.g., 'transaction','budget','project'
  entity_id INT NOT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  performed_by INT,
  performed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- indexes for performance
CREATE INDEX idx_transactions_budget ON transactions(budget_id);
CREATE INDEX idx_projects_budget ON projects(budget_id);

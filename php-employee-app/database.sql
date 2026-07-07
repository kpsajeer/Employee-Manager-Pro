-- ============================================================
-- Employee Manager – database schema + seed data
-- MySQL 8.0+ / MariaDB 10.5+
--
-- Usage:
--   mysql -u root -p < database.sql
--   or import via phpMyAdmin / TablePlus / DBeaver
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS `employee_manager`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `employee_manager`;

-- ── Table ────────────────────────────────────────────────────
DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `first_name`  VARCHAR(100)    NOT NULL,
    `last_name`   VARCHAR(100)    NOT NULL,
    `email`       VARCHAR(255)    NOT NULL,
    `phone`       VARCHAR(30)     DEFAULT NULL,
    `department`  VARCHAR(100)    NOT NULL,
    `position`    VARCHAR(150)    NOT NULL,
    `salary`      DECIMAL(12, 2)  NOT NULL DEFAULT 0.00,
    `date_joined` DATE            NOT NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_employees_email` (`email`),
    KEY `idx_employees_department` (`department`),
    KEY `idx_employees_status` (`status`),
    KEY `idx_employees_name` (`last_name`, `first_name`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ── Seed data – 20 sample employees ──────────────────────────
INSERT INTO `employees`
    (`first_name`, `last_name`, `email`, `phone`, `department`, `position`, `salary`, `date_joined`, `status`)
VALUES
    ('Alice',    'Johnson',   'alice.johnson@example.com',   '+1 555-101-0001', 'Engineering',      'Senior Software Engineer',   105000.00, '2020-03-15', 'active'),
    ('Bob',      'Martinez',  'bob.martinez@example.com',    '+1 555-101-0002', 'Engineering',      'Backend Developer',           82000.00, '2021-07-01', 'active'),
    ('Carol',    'Williams',  'carol.williams@example.com',  '+1 555-101-0003', 'Marketing',        'Marketing Manager',           90000.00, '2019-11-20', 'active'),
    ('David',    'Brown',     'david.brown@example.com',     '+1 555-101-0004', 'Sales',            'Sales Representative',        62000.00, '2022-01-10', 'active'),
    ('Eva',      'Davis',     'eva.davis@example.com',       '+1 555-101-0005', 'Human Resources',  'HR Specialist',               70000.00, '2020-08-05', 'active'),
    ('Frank',    'Miller',    'frank.miller@example.com',    '+1 555-101-0006', 'Finance',          'Financial Analyst',           88000.00, '2018-06-14', 'active'),
    ('Grace',    'Wilson',    'grace.wilson@example.com',    '+1 555-101-0007', 'Design',           'UI/UX Designer',              80000.00, '2021-03-22', 'active'),
    ('Henry',    'Moore',     'henry.moore@example.com',     '+1 555-101-0008', 'Operations',       'Operations Manager',          95000.00, '2017-09-30', 'active'),
    ('Irene',    'Taylor',    'irene.taylor@example.com',    '+1 555-101-0009', 'Product',          'Product Manager',            115000.00, '2020-05-12', 'active'),
    ('Jack',     'Anderson',  'jack.anderson@example.com',   '+1 555-101-0010', 'Engineering',      'DevOps Engineer',             98000.00, '2019-02-28', 'active'),
    ('Karen',    'Thomas',    'karen.thomas@example.com',    '+1 555-101-0011', 'Customer Support', 'Support Lead',                65000.00, '2021-10-01', 'active'),
    ('Leo',      'Jackson',   'leo.jackson@example.com',     '+1 555-101-0012', 'Legal',            'Legal Counsel',              120000.00, '2016-04-18', 'active'),
    ('Maria',    'White',     'maria.white@example.com',     '+1 555-101-0013', 'Marketing',        'Content Strategist',          72000.00, '2022-06-07', 'active'),
    ('Nathan',   'Harris',    'nathan.harris@example.com',   '+1 555-101-0014', 'Sales',            'Account Executive',           75000.00, '2020-09-15', 'inactive'),
    ('Olivia',   'Martin',    'olivia.martin@example.com',   '+1 555-101-0015', 'Engineering',      'Frontend Developer',          84000.00, '2021-12-01', 'active'),
    ('Peter',    'Garcia',    'peter.garcia@example.com',    '+1 555-101-0016', 'Finance',          'Accountant',                  68000.00, '2019-08-20', 'inactive'),
    ('Quinn',    'Martinez',  'quinn.martinez@example.com',  '+1 555-101-0017', 'Design',           'Graphic Designer',            62000.00, '2023-01-16', 'active'),
    ('Rachel',   'Robinson',  'rachel.robinson@example.com', '+1 555-101-0018', 'Human Resources',  'Recruitment Manager',         85000.00, '2018-11-05', 'active'),
    ('Samuel',   'Clark',     'samuel.clark@example.com',    '+1 555-101-0019', 'Operations',       'Logistics Coordinator',       58000.00, '2022-03-28', 'inactive'),
    ('Tina',     'Lewis',     'tina.lewis@example.com',      '+1 555-101-0020', 'Product',          'Business Analyst',            92000.00, '2020-07-13', 'active');

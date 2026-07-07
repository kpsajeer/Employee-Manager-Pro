-- ============================================================
-- Employee Manager – PostgreSQL schema + seed data
--
-- Runs automatically on first boot via start.sh / setup.php.
-- You can also run it manually:
--   psql "$DATABASE_URL" -f database.sql
-- ============================================================

-- ── Table ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS employees (
    id          SERIAL          PRIMARY KEY,
    first_name  VARCHAR(100)    NOT NULL,
    last_name   VARCHAR(100)    NOT NULL,
    email       VARCHAR(255)    NOT NULL,
    phone       VARCHAR(30)     DEFAULT NULL,
    department  VARCHAR(100)    NOT NULL,
    position    VARCHAR(150)    NOT NULL,
    salary      NUMERIC(12, 2)  NOT NULL DEFAULT 0.00,
    date_joined DATE            NOT NULL,
    status      VARCHAR(10)     NOT NULL DEFAULT 'active'
                                CHECK (status IN ('active', 'inactive')),
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Unique email constraint
ALTER TABLE employees
    DROP CONSTRAINT IF EXISTS uq_employees_email;
ALTER TABLE employees
    ADD CONSTRAINT uq_employees_email UNIQUE (email);

-- Indexes for common filter / sort columns
CREATE INDEX IF NOT EXISTS idx_employees_department ON employees (department);
CREATE INDEX IF NOT EXISTS idx_employees_status     ON employees (status);
CREATE INDEX IF NOT EXISTS idx_employees_name       ON employees (last_name, first_name);

-- ── Trigger: auto-update updated_at on row change ────────────
CREATE OR REPLACE FUNCTION fn_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_employees_updated_at ON employees;
CREATE TRIGGER trg_employees_updated_at
    BEFORE UPDATE ON employees
    FOR EACH ROW EXECUTE FUNCTION fn_set_updated_at();

-- ── Seed data – 20 sample employees ──────────────────────────
INSERT INTO employees
    (first_name, last_name, email, phone, department, position, salary, date_joined, status)
VALUES
    ('Alice',  'Johnson',  'alice.johnson@example.com',   '+1 555-101-0001', 'Engineering',      'Senior Software Engineer',  105000.00, '2020-03-15', 'active'),
    ('Bob',    'Martinez', 'bob.martinez@example.com',    '+1 555-101-0002', 'Engineering',      'Backend Developer',          82000.00, '2021-07-01', 'active'),
    ('Carol',  'Williams', 'carol.williams@example.com',  '+1 555-101-0003', 'Marketing',        'Marketing Manager',          90000.00, '2019-11-20', 'active'),
    ('David',  'Brown',    'david.brown@example.com',     '+1 555-101-0004', 'Sales',            'Sales Representative',       62000.00, '2022-01-10', 'active'),
    ('Eva',    'Davis',    'eva.davis@example.com',       '+1 555-101-0005', 'Human Resources',  'HR Specialist',              70000.00, '2020-08-05', 'active'),
    ('Frank',  'Miller',   'frank.miller@example.com',    '+1 555-101-0006', 'Finance',          'Financial Analyst',          88000.00, '2018-06-14', 'active'),
    ('Grace',  'Wilson',   'grace.wilson@example.com',    '+1 555-101-0007', 'Design',           'UI/UX Designer',             80000.00, '2021-03-22', 'active'),
    ('Henry',  'Moore',    'henry.moore@example.com',     '+1 555-101-0008', 'Operations',       'Operations Manager',         95000.00, '2017-09-30', 'active'),
    ('Irene',  'Taylor',   'irene.taylor@example.com',    '+1 555-101-0009', 'Product',          'Product Manager',           115000.00, '2020-05-12', 'active'),
    ('Jack',   'Anderson', 'jack.anderson@example.com',   '+1 555-101-0010', 'Engineering',      'DevOps Engineer',            98000.00, '2019-02-28', 'active'),
    ('Karen',  'Thomas',   'karen.thomas@example.com',    '+1 555-101-0011', 'Customer Support', 'Support Lead',               65000.00, '2021-10-01', 'active'),
    ('Leo',    'Jackson',  'leo.jackson@example.com',     '+1 555-101-0012', 'Legal',            'Legal Counsel',             120000.00, '2016-04-18', 'active'),
    ('Maria',  'White',    'maria.white@example.com',     '+1 555-101-0013', 'Marketing',        'Content Strategist',         72000.00, '2022-06-07', 'active'),
    ('Nathan', 'Harris',   'nathan.harris@example.com',   '+1 555-101-0014', 'Sales',            'Account Executive',          75000.00, '2020-09-15', 'inactive'),
    ('Olivia', 'Martin',   'olivia.martin@example.com',   '+1 555-101-0015', 'Engineering',      'Frontend Developer',         84000.00, '2021-12-01', 'active'),
    ('Peter',  'Garcia',   'peter.garcia@example.com',    '+1 555-101-0016', 'Finance',          'Accountant',                 68000.00, '2019-08-20', 'inactive'),
    ('Quinn',  'Martinez', 'quinn.martinez@example.com',  '+1 555-101-0017', 'Design',           'Graphic Designer',           62000.00, '2023-01-16', 'active'),
    ('Rachel', 'Robinson', 'rachel.robinson@example.com', '+1 555-101-0018', 'Human Resources',  'Recruitment Manager',        85000.00, '2018-11-05', 'active'),
    ('Samuel', 'Clark',    'samuel.clark@example.com',    '+1 555-101-0019', 'Operations',       'Logistics Coordinator',      58000.00, '2022-03-28', 'inactive'),
    ('Tina',   'Lewis',    'tina.lewis@example.com',      '+1 555-101-0020', 'Product',          'Business Analyst',           92000.00, '2020-07-13', 'active')
ON CONFLICT (email) DO NOTHING;

-- ============================================================
-- PIXOUS HR ADMIN PORTAL — MySQL Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS `pixous_hr` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pixous_hr`;

-- ── Admin Users ──
CREATE TABLE `users` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `full_name`  VARCHAR(100) NOT NULL,
    `email`      VARCHAR(100) NOT NULL,
    `role`       ENUM('admin','manager','hr') NOT NULL DEFAULT 'hr',
    `avatar`     VARCHAR(255) DEFAULT NULL,
    `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Departments ──
CREATE TABLE `departments` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(100) NOT NULL UNIQUE,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Designations ──
CREATE TABLE `designations` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `title`      VARCHAR(100) NOT NULL UNIQUE,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Employees ──
CREATE TABLE `employees` (
    `id`              INT AUTO_INCREMENT PRIMARY KEY,
    `emp_code`        VARCHAR(20)  NOT NULL UNIQUE,
    `name`            VARCHAR(100) NOT NULL,
    `father_name`     VARCHAR(100) DEFAULT NULL,
    `gender`          ENUM('Male','Female','Other') NOT NULL DEFAULT 'Male',
    `marital_status`  ENUM('Married','Unmarried') NOT NULL DEFAULT 'Unmarried',
    `dob`             DATE DEFAULT NULL,
    `doj`             DATE DEFAULT NULL,
    `department_id`   INT DEFAULT NULL,
    `designation_id`  INT DEFAULT NULL,
    `qualification`   VARCHAR(100) DEFAULT NULL,
    `experience`      VARCHAR(50)  DEFAULT NULL,
    `mobile`          VARCHAR(15)  NOT NULL,
    `email`           VARCHAR(100) DEFAULT NULL,
    `address`         TEXT DEFAULT NULL,
    `pan`             VARCHAR(20)  DEFAULT NULL,
    `aadhar`          VARCHAR(20)  DEFAULT NULL,
    `uan`             VARCHAR(30)  DEFAULT NULL,
    `esi_no`          VARCHAR(30)  DEFAULT NULL,
    `bank_name`       VARCHAR(100) DEFAULT NULL,
    `bank_account`    VARCHAR(30)  DEFAULT NULL,
    `bank_ifsc`       VARCHAR(20)  DEFAULT NULL,
    `salary`          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `photo`           VARCHAR(255) DEFAULT NULL,
    `status`          ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `nominee_name`    VARCHAR(100) DEFAULT NULL,
    `nominee_relation`VARCHAR(50)  DEFAULT NULL,
    `nominee_address` TEXT DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`department_id`)  REFERENCES `departments`(`id`)  ON DELETE SET NULL,
    FOREIGN KEY (`designation_id`) REFERENCES `designations`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_emp_code` (`emp_code`)
) ENGINE=InnoDB;

-- ── Leave Types ──
CREATE TABLE `leave_types` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(50) NOT NULL UNIQUE,
    `days_allowed` INT NOT NULL DEFAULT 12
) ENGINE=InnoDB;

-- ── Leave Requests ──
CREATE TABLE `leave_requests` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id`  INT NOT NULL,
    `leave_type_id`INT NOT NULL,
    `date_from`    DATE NOT NULL,
    `date_to`      DATE NOT NULL,
    `days`         INT NOT NULL DEFAULT 1,
    `reason`       TEXT NOT NULL,
    `status`       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by`  INT DEFAULT NULL,
    `remarks`      TEXT DEFAULT NULL,
    `applied_on`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`)   REFERENCES `employees`(`id`)   ON DELETE CASCADE,
    FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`)   REFERENCES `users`(`id`)       ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_employee` (`employee_id`)
) ENGINE=InnoDB;

-- ── Payroll ──
CREATE TABLE `payroll` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id`    INT NOT NULL,
    `month`          TINYINT NOT NULL,
    `year`           SMALLINT NOT NULL,
    `basic`          DECIMAL(12,2) NOT NULL DEFAULT 0,
    `hra`            DECIMAL(12,2) NOT NULL DEFAULT 0,
    `da`             DECIMAL(12,2) NOT NULL DEFAULT 0,
    `special_allow`  DECIMAL(12,2) NOT NULL DEFAULT 0,
    `gross`          DECIMAL(12,2) NOT NULL DEFAULT 0,
    `pf`             DECIMAL(12,2) NOT NULL DEFAULT 0,
    `esi`            DECIMAL(12,2) NOT NULL DEFAULT 0,
    `prof_tax`       DECIMAL(12,2) NOT NULL DEFAULT 0,
    `other_deduct`   DECIMAL(12,2) NOT NULL DEFAULT 0,
    `net_pay`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `status`         ENUM('draft','processed','paid') NOT NULL DEFAULT 'draft',
    `processed_by`   INT DEFAULT NULL,
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`)  REFERENCES `employees`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`)     ON DELETE SET NULL,
    UNIQUE KEY `uk_emp_month` (`employee_id`, `month`, `year`),
    INDEX `idx_period` (`year`, `month`)
) ENGINE=InnoDB;

-- ── Tasks ──
CREATE TABLE `tasks` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `title`        VARCHAR(200) NOT NULL,
    `description`  TEXT DEFAULT NULL,
    `assigned_to`  INT NOT NULL,
    `priority`     ENUM('High','Medium','Low') NOT NULL DEFAULT 'Medium',
    `status`       ENUM('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
    `deadline`     DATE NOT NULL,
    `progress`     TINYINT NOT NULL DEFAULT 0,
    `created_by`   INT DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assigned_to`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`)     ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_assigned` (`assigned_to`)
) ENGINE=InnoDB;

-- ── Activity Log ──
CREATE TABLE `activity_log` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT DEFAULT NULL,
    `action`     VARCHAR(100) NOT NULL,
    `module`     VARCHAR(50)  NOT NULL,
    `details`    TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45)  DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;


-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin (password: admin123)
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@pixous.com', 'admin');

-- Departments
INSERT INTO `departments` (`name`) VALUES
('Engineering'),('HR'),('Production'),('Accounts'),('Maintenance'),('Packing'),('Stores'),('Admin'),('Quality');

-- Designations
INSERT INTO `designations` (`title`) VALUES
('Sr. Engineer'),('Engineer'),('HR Executive'),('Supervisor'),('Sr. Accountant'),
('Accountant'),('Operator'),('Helper'),('Electrician'),('Packer'),
('Store Keeper'),('Manager'),('Team Lead');

-- Leave Types
INSERT INTO `leave_types` (`name`, `days_allowed`) VALUES
('Casual Leave',12),('Sick Leave',10),('Annual Leave',15),
('Maternity Leave',180),('Paternity Leave',15),('Comp Off',5);

-- Employees (from uploaded ESI/PF file)
INSERT INTO `employees` (`emp_code`,`name`,`father_name`,`gender`,`marital_status`,`dob`,`doj`,`department_id`,`designation_id`,`qualification`,`experience`,`mobile`,`email`,`address`,`pan`,`aadhar`,`bank_name`,`bank_account`,`bank_ifsc`,`salary`,`status`) VALUES
('EMP001','Arif K','Karthikeyan M','Male','Married','1998-10-15','2020-04-08',1,1,'Diploma (EEE)','8 years','7339443444','arifsabi1730@gmail.com','42C, Siva Complex, State Bank Nagar, Chettipalayam, Erode','HTTPK1776E','428602962905','Axis Bank','922010049646614','UTIB0000785',35000.00,'active'),
('EMP002','Dinesh S','Selvaraj K','Male','Unmarried','2003-03-15','2020-04-06',2,3,'MBA','1 year','6385514511','dinesh.smba2003@gmail.com','22-1, Shanmuga Nagar, Vediyarasampalayam, Namakkal','HAVPD3078K','924914986337','Karur Vysya Bank','1654155000098520','KVBL0001654',28000.00,'active'),
('EMP003','Dinesh R','Rajamanikam S','Male','Unmarried','1997-06-01','2020-04-16',3,4,'Diploma (Textile)','5 years','9500250212','dt7661300@gmail.com','5/91-C, Nallagoundamapalayam, Tiruchengode','FMZPR1737P','962272864493','Indian Bank','6849439766','IDIB000A194',32000.00,'active'),
('EMP004','Rajkumar S','Selvaraj R','Male','Married','1979-02-10','2020-04-08',4,5,'B.Com','20 years','8675118810','srajkumarerd@gmail.com','162/1A, Eraniyan Street, Manicka Vasakar Colony, Erode','AIEPR9108A','578699515379','State Bank of India','38164349599','SBIN0012779',45000.00,'active'),
('EMP005','Chelladurai M','Murugan','Male','Married','1971-09-15','2020-04-13',3,7,'SSLC','2 years','9751301797','','24-4, North Street, Poolampatti, Dindigul','BWAPC5280K','544309226282','Indian Overseas Bank','183201000017716','IOBA0001832',22000.00,'active'),
('EMP006','Subramani P V','Venkatachalam','Male','Married','1959-06-10','2020-04-06',5,8,'8th Std','1 year','9095291263','','228, 1010 Nesavalar Colony, Mukasipidariyur, Erode','GQKPS9569G','449265315390','Union Bank of India','747502010002469','UBIN0574759',18000.00,'inactive'),
('EMP007','Palanisamy R','Ramasamy','Male','Married','1957-01-05','2020-04-06',3,7,'NA','1 year','7339214743','','24/22, Ariyankadu, Kanjikoil, Erode','GKJPP3491P','776212223909','State Bank of India','34944944455','SBIN0014178',20000.00,'inactive'),
('EMP008','Selvi P','Palanisamy','Female','Married','1971-06-10','2020-04-07',3,7,'NA','2 years','8973431334','','4/151, Neikkaranpalayam, Alambadi, Kangeyam, Erode','ONFPS2638Q','303592505301','Canara Bank','3129119000412','CNRB0003129',19000.00,'active'),
('EMP009','Maniyal A','Arumugam','Female','Married','1970-04-05','2020-04-07',3,7,'NA','2 years','9095599845','','4/162, Neikkaranpalayam, Alambadi, Kangeyam, Erode','HVNPM4900C','695214814738','Canara Bank','3129119000364','CNRB0003129',19000.00,'active'),
('EMP010','Pushpa M','Manokaran','Female','Married','1979-05-05','2020-04-07',3,7,'8th Std','2 years','9751522099','','135, Neikkaranpalayam, Alambadi, Kangeyam, Erode','GGHPP1778M','786839887361','Canara Bank','3129108001467','CNRB0003129',19500.00,'active'),
('EMP011','Devi M','Murugan','Female','Married','1987-12-20','2020-04-25',6,10,'8th Std','6 months','9361228844','','57, Kaasupillam Palayam, Kambilliyampatti, Perundurai, Erode','EWBPD9660N','501145508542','State Bank of India','43053069392','SBIN0021741',17000.00,'active'),
('EMP012','K Jothimani','Karuppusamy','Female','Married','1982-08-15','2020-04-25',6,10,'NA','6 months','9385988353','','SNO31, Kasipillampalayam, Kambaliyampatti, Erode','AUWPJ5133H','804750171293','Indian Overseas Bank','183201000017566','IOBA0001832',17000.00,'active'),
('EMP013','Sivakumar R','Ramadoss','Male','Married','1980-06-15','2020-04-28',7,11,'SSLC','1 year','8883146491','','563/1 Meenavar Street, Kameswaram, Nagapattinam','FOQPR0060Q','238283189970','Karur Vysya Bank','116715500014972','KVBL0001167',23000.00,'active'),
('EMP014','Muthusami P','Palanisamy','Male','Married','1967-10-15','2020-04-07',5,9,'8th Std','2 years','9842322510','','226, Chennimalai, 1010 Nesavalar Colony, Erode','AVJPM1322R','210538499323','Union Bank of India','747502010002633','UBIN0574759',24000.00,'active'),
('EMP015','Jothi P','Palanisamy','Female','Married','1971-02-25','2020-04-27',3,7,'NA','1 year','9585349765','','81/1 Pudhupalayam, Ichipalayam, Uthukuli, Tirupur','BJRPJ8329L','314623968744','Bank of Baroda','5500100011722','BARB0UTTUKU',19000.00,'active');

-- Sample Leave Requests
INSERT INTO `leave_requests` (`employee_id`,`leave_type_id`,`date_from`,`date_to`,`days`,`reason`,`status`) VALUES
(1,2,'2026-05-10','2026-05-11',2,'Fever and cold','pending'),
(2,1,'2026-05-15','2026-05-15',1,'Personal work','approved'),
(4,3,'2026-05-20','2026-05-25',6,'Family vacation','pending'),
(8,2,'2026-04-28','2026-04-29',2,'Hospital visit','rejected'),
(11,1,'2026-05-12','2026-05-12',1,'School event','pending');

-- Sample Tasks
INSERT INTO `tasks` (`title`,`description`,`assigned_to`,`priority`,`status`,`deadline`,`progress`) VALUES
('Monthly Payroll Processing','Process May 2026 payroll for all departments',4,'High','In Progress','2026-05-10',65),
('Employee Onboarding - New Batch','Complete onboarding for 5 new hires',2,'High','Pending','2026-05-15',10),
('Machine Maintenance Schedule','Quarterly maintenance check for production floor',3,'Medium','In Progress','2026-05-12',40),
('Safety Audit Report','Complete annual safety audit for factory',1,'High','Completed','2026-05-05',100),
('Inventory Stock Check','Monthly inventory reconciliation',13,'Medium','In Progress','2026-05-08',55),
('Quality Check Documentation','Update QC docs for ISO compliance',3,'Low','Pending','2026-05-20',0),
('Annual Leave Balance Update','Reconcile leave balances for all employees',2,'Low','Completed','2026-05-01',100);

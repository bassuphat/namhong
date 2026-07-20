-- ==========================================================================
-- NAMHONG GROUP — database schema
-- วิธีใช้: เข้า cPanel → phpMyAdmin → เลือกฐานข้อมูลที่สร้างไว้ → แท็บ SQL
-- วางไฟล์นี้ทั้งหมดแล้วกด Go ครั้งเดียว จะได้ตาราง 3 ตารางพร้อมใช้งาน
-- ==========================================================================

CREATE TABLE IF NOT EXISTS contact_messages (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname     VARCHAR(150) NOT NULL,
  company      VARCHAR(150) NULL,
  email        VARCHAR(150) NOT NULL,
  phone        VARCHAR(30)  NOT NULL,
  subject      VARCHAR(150) NOT NULL,
  message      TEXT NOT NULL,
  ip_address   VARCHAR(45) NULL,
  is_read      TINYINT(1) NOT NULL DEFAULT 0,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created (created_at),
  INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_applications (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname     VARCHAR(150) NOT NULL,
  position     VARCHAR(150) NOT NULL,
  email        VARCHAR(150) NOT NULL,
  phone        VARCHAR(30)  NOT NULL,
  message      TEXT NULL,
  ip_address   VARCHAR(45) NULL,
  is_read      TINYINT(1) NOT NULL DEFAULT 0,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created (created_at),
  INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- basic rate-limiting log (one row per submission attempt, old rows can be
-- purged periodically — not required, just keeps the table small)
CREATE TABLE IF NOT EXISTS submission_log (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip_address   VARCHAR(45) NOT NULL,
  form_name    VARCHAR(30) NOT NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

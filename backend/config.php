<?php
/**
 * NAMHONG GROUP — backend configuration
 * ---------------------------------------------------------------------
 * กรุณาแก้ไขค่าด้านล่างให้ตรงกับฐานข้อมูล MySQL จริงบน Hostatom
 * หาได้จาก cPanel → MySQL Databases (หลังสร้างฐานข้อมูล + ผู้ใช้ + ผูกสิทธิ์แล้ว)
 * Hostatom (และ cPanel ทั่วไป) มักตั้งชื่อฐานข้อมูล/ผู้ใช้แบบ cpaneluser_ชื่อที่ตั้ง
 * เช่น ถ้า cPanel username คือ namhong และตั้งชื่อฐานข้อมูลว่า site
 * ชื่อจริงมักจะกลายเป็น namhong_site โดยอัตโนมัติ — คัดลอกชื่อที่ระบบสร้างให้แม่นยำ
 * ---------------------------------------------------------------------
 */

define('DB_HOST', 'localhost');            // Hostatom ใช้ localhost แทบทุกกรณี
define('DB_NAME', 'CHANGE_ME_dbname');     // กรุณาใส่ชื่อฐานข้อมูลจริง
define('DB_USER', 'CHANGE_ME_dbuser');     // กรุณาใส่ชื่อผู้ใช้ฐานข้อมูลจริง
define('DB_PASS', 'CHANGE_ME_password');   // กรุณาใส่รหัสผ่านจริง (ตั้งรหัสที่คาดเดายากไว้)

// เวลาหมดอายุของ session ผู้ดูแลระบบ (วินาที) — ปัจจุบัน 4 ชั่วโมง
define('SESSION_LIFETIME', 60 * 60 * 4);

// จำกัดจำนวนครั้งที่ IP เดียวกันส่งฟอร์มได้ ภายในกี่นาที (กันสแปมเบื้องต้น)
define('RATE_LIMIT_MAX', 5);
define('RATE_LIMIT_MINUTES', 10);

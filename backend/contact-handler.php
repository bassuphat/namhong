<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_respond(false, 'Method not allowed');
}

// Honeypot field: a real visitor never fills this in (it's hidden by CSS).
// A bot that fills every field will trip it — we pretend to succeed so the
// bot has no signal, but we never touch the database.
if (trim($_POST['website'] ?? '') !== '') {
    json_respond(true, 'ส่งข้อความเรียบร้อยแล้ว ขอบคุณที่ติดต่อนามหงส์ กรุ๊ป');
}

$fullname = trim((string)($_POST['fullname'] ?? ''));
$company  = trim((string)($_POST['company']  ?? ''));
$email    = trim((string)($_POST['email']    ?? ''));
$phone    = trim((string)($_POST['phone']    ?? ''));
$subject  = trim((string)($_POST['subject']  ?? ''));
$message  = trim((string)($_POST['message']  ?? ''));
$consent  = (string)($_POST['consent'] ?? '');

$errors = [];
if ($fullname === '' || mb_strlen($fullname) > 150) $errors[] = 'กรุณากรอกชื่อ-นามสกุลให้ถูกต้อง';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'กรุณากรอกอีเมลให้ถูกต้อง';
if ($phone === '' || mb_strlen($phone) > 30) $errors[] = 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง';
if ($subject === '') $errors[] = 'กรุณาเลือกเรื่องที่ติดต่อ';
if ($message === '' || mb_strlen($message) > 5000) $errors[] = 'กรุณากรอกข้อความ (ไม่เกิน 5000 ตัวอักษร)';
if ($consent === '') $errors[] = 'กรุณายินยอมนโยบายความเป็นส่วนตัวก่อนส่งข้อความ';

if ($errors) {
    json_respond(false, implode(' / ', $errors));
}

try {
    $pdo = get_db();
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (is_rate_limited($pdo, $ip, 'contact')) {
        json_respond(false, 'มีการส่งข้อความบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (fullname, company, email, phone, subject, message, ip_address)
         VALUES (:fullname, :company, :email, :phone, :subject, :message, :ip)'
    );
    $stmt->execute([
        ':fullname' => $fullname,
        ':company'  => $company !== '' ? $company : null,
        ':email'    => $email,
        ':phone'    => $phone,
        ':subject'  => $subject,
        ':message'  => $message,
        ':ip'       => $ip,
    ]);

    json_respond(true, 'ส่งข้อความเรียบร้อยแล้ว ขอบคุณที่ติดต่อนามหงส์ กรุ๊ป ทีมงานจะติดต่อกลับโดยเร็วที่สุด');
} catch (Throwable $e) {
    error_log('contact-handler: ' . $e->getMessage());
    json_respond(false, 'ระบบขัดข้อง กรุณาลองใหม่อีกครั้ง หรือติดต่อเราทางอีเมล/โทรศัพท์โดยตรง');
}

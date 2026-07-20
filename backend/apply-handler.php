<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_respond(false, 'Method not allowed');
}

// Honeypot — see contact-handler.php for the same pattern explained.
if (trim($_POST['website'] ?? '') !== '') {
    json_respond(true, 'ส่งใบสมัครเรียบร้อยแล้ว');
}

$fullname = trim((string)($_POST['fullname'] ?? ''));
$position = trim((string)($_POST['position'] ?? ''));
$email    = trim((string)($_POST['email']    ?? ''));
$phone    = trim((string)($_POST['phone']    ?? ''));
$message  = trim((string)($_POST['message']  ?? ''));
$consent  = (string)($_POST['consent'] ?? '');

$errors = [];
if ($fullname === '' || mb_strlen($fullname) > 150) $errors[] = 'กรุณากรอกชื่อ-นามสกุลให้ถูกต้อง';
if ($position === '' || mb_strlen($position) > 150) $errors[] = 'กรุณากรอกตำแหน่งที่สนใจ';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'กรุณากรอกอีเมลให้ถูกต้อง';
if ($phone === '' || mb_strlen($phone) > 30) $errors[] = 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง';
if (mb_strlen($message) > 5000) $errors[] = 'ข้อความยาวเกินไป (ไม่เกิน 5000 ตัวอักษร)';
if ($consent === '') $errors[] = 'กรุณายินยอมนโยบายความเป็นส่วนตัวก่อนส่งใบสมัคร';

if ($errors) {
    json_respond(false, implode(' / ', $errors));
}

try {
    $pdo = get_db();
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if (is_rate_limited($pdo, $ip, 'job-application')) {
        json_respond(false, 'มีการส่งใบสมัครบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO job_applications (fullname, position, email, phone, message, ip_address)
         VALUES (:fullname, :position, :email, :phone, :message, :ip)'
    );
    $stmt->execute([
        ':fullname' => $fullname,
        ':position' => $position,
        ':email'    => $email,
        ':phone'    => $phone,
        ':message'  => $message !== '' ? $message : null,
        ':ip'       => $ip,
    ]);

    json_respond(true, 'ส่งใบสมัครเรียบร้อยแล้ว ทีมงานจะติดต่อกลับหากคุณสมบัติตรงกับตำแหน่งที่เปิดรับ');
} catch (Throwable $e) {
    error_log('apply-handler: ' . $e->getMessage());
    json_respond(false, 'ระบบขัดข้อง กรุณาลองใหม่อีกครั้ง หรือส่งอีเมลใบสมัครมาโดยตรง');
}

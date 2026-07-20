<?php
declare(strict_types=1);
require_once __DIR__ . '/../db.php';

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * ⚠️ ไฟล์นี้ใช้สร้างบัญชีผู้ดูแลระบบ "คนแรก" เท่านั้น
 * ทำงานได้แค่ตอนที่ยังไม่มีผู้ดูแลระบบในฐานข้อมูลเลย (กันไม่ให้ใครใช้ไฟล์นี้
 * สร้างบัญชีแอบเพิ่มทีหลังได้ แม้จะลืมลบไฟล์นี้ทิ้ง)
 * เมื่อสร้างบัญชีสำเร็จแล้ว "กรุณาลบไฟล์นี้ออกจากเซิร์ฟเวอร์ทันที"
 */

$message = '';
$done = false;

try {
    $pdo = get_db();
    $existing = (int) $pdo->query('SELECT COUNT(*) AS n FROM admin_users')->fetch()['n'];
} catch (Throwable $e) {
    $existing = -1;
    $message = 'เชื่อมต่อฐานข้อมูลไม่ได้ กรุณาตรวจสอบ config.php และรัน schema.sql ก่อน (' . $e->getMessage() . ')';
}

if ($existing > 0) {
    $message = 'มีบัญชีผู้ดูแลระบบอยู่แล้ว ไฟล์นี้จึงถูกปิดการใช้งานเพื่อความปลอดภัย กรุณาลบไฟล์นี้ทิ้ง';
} elseif ($existing === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || mb_strlen($username) < 3) {
        $message = 'กรุณาตั้งชื่อผู้ใช้อย่างน้อย 3 ตัวอักษร';
    } elseif (mb_strlen($password) < 8) {
        $message = 'กรุณาตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร';
    } else {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)');
        $stmt->execute([':u' => $username, ':p' => password_hash($password, PASSWORD_DEFAULT)]);
        $done = true;
        $message = 'สร้างบัญชีผู้ดูแลระบบสำเร็จ! กรุณาลบไฟล์ create-admin.php ทิ้งทันที แล้วไปเข้าสู่ระบบที่ login.php';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<title>ตั้งค่าบัญชีผู้ดูแลระบบครั้งแรก | นามหงส์ กรุ๊ป</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="center-wrap">
  <div class="card">
    <div class="brand">นามหงส์ กรุ๊ป</div>
    <h1>ตั้งค่าบัญชีผู้ดูแลระบบครั้งแรก</h1>
    <p class="muted" style="margin-bottom:20px;">ใช้ครั้งเดียวตอนติดตั้งระบบ แล้วต้องลบไฟล์นี้ทิ้ง</p>

    <?php if ($existing !== 0): ?>
      <div class="alert alert-warn"><?= h($message) ?></div>
      <?php if ($existing > 0): ?><a href="login.php" class="btn">ไปหน้าเข้าสู่ระบบ</a><?php endif; ?>
    <?php elseif ($done): ?>
      <div class="alert alert-warn"><?= h($message) ?></div>
      <a href="login.php" class="btn">ไปหน้าเข้าสู่ระบบ</a>
    <?php else: ?>
      <?php if ($message !== ''): ?><div class="alert alert-err"><?= h($message) ?></div><?php endif; ?>
      <form method="post" novalidate>
        <div class="field">
          <label for="username">ชื่อผู้ใช้ที่ต้องการ</label>
          <input type="text" id="username" name="username" required minlength="3" autofocus>
        </div>
        <div class="field">
          <label for="password">ตั้งรหัสผ่าน (อย่างน้อย 8 ตัวอักษร)</label>
          <input type="password" id="password" name="password" required minlength="8">
        </div>
        <button type="submit" class="btn" style="width:100%;">สร้างบัญชี</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

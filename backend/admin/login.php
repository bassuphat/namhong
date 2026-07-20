<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check((string)($_POST['csrf'] ?? ''))) {
        $error = 'เซสชันหมดอายุ กรุณาลองใหม่อีกครั้ง';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        try {
            $pdo = get_db();
            $stmt = $pdo->prepare('SELECT id, password_hash FROM admin_users WHERE username = :u LIMIT 1');
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();
            if ($row && password_verify($password, $row['password_hash'])) {
                session_regenerate_id(true); // prevent session fixation
                $_SESSION['admin_id'] = (int) $row['id'];
                $_SESSION['admin_username'] = $username;
                $_SESSION['last_active'] = time();
                header('Location: dashboard.php');
                exit;
            }
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        } catch (Throwable $e) {
            error_log('login.php: ' . $e->getMessage());
            $error = 'ระบบขัดข้อง กรุณาลองใหม่อีกครั้ง (ตรวจสอบว่าตั้งค่า config.php และรัน schema.sql แล้วหรือยัง)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<title>เข้าสู่ระบบผู้ดูแล | นามหงส์ กรุ๊ป</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="center-wrap">
  <div class="card">
    <div class="brand">นามหงส์ กรุ๊ป</div>
    <h1>เข้าสู่ระบบผู้ดูแล</h1>
    <p class="muted" style="margin-bottom:20px;">สำหรับดูข้อความติดต่อและใบสมัครงานที่เข้ามา</p>
    <?php if (isset($_GET['expired'])): ?>
      <div class="alert alert-warn">เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่</div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <div class="alert alert-err"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <div class="field">
        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username">
      </div>
      <div class="field">
        <label for="password">รหัสผ่าน</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn" style="width:100%;">เข้าสู่ระบบ</button>
    </form>
  </div>
</div>
</body>
</html>

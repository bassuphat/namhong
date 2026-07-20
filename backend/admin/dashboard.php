<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();

$pdo = get_db();
$flash = '';

// ---- handle actions (mark read / delete) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check((string)($_POST['csrf'] ?? ''))) {
        $flash = 'เซสชันหมดอายุ กรุณาลองใหม่';
    } else {
        $table  = ($_POST['table'] ?? '') === 'job_applications' ? 'job_applications' : 'contact_messages';
        $id     = (int) ($_POST['id'] ?? 0);
        $action = (string) ($_POST['do'] ?? '');
        if ($id > 0 && in_array($action, ['read', 'unread', 'delete'], true)) {
            if ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = :id");
            } else {
                $val  = $action === 'read' ? 1 : 0;
                $stmt = $pdo->prepare("UPDATE {$table} SET is_read = {$val} WHERE id = :id");
            }
            $stmt->execute([':id' => $id]);
        }
    }
    header('Location: dashboard.php?tab=' . (($_GET['tab'] ?? '') === 'jobs' ? 'jobs' : 'contact'));
    exit;
}

$contacts = $pdo->query('SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC LIMIT 200')->fetchAll();
$jobs     = $pdo->query('SELECT * FROM job_applications ORDER BY is_read ASC, created_at DESC LIMIT 200')->fetchAll();
$unreadContacts = $pdo->query('SELECT COUNT(*) AS n FROM contact_messages WHERE is_read = 0')->fetch()['n'];
$unreadJobs     = $pdo->query('SELECT COUNT(*) AS n FROM job_applications WHERE is_read = 0')->fetch()['n'];
$activeTab = ($_GET['tab'] ?? 'contact') === 'jobs' ? 'jobs' : 'contact';
$token = csrf_token();

function fmt_date(string $dt): string
{
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y H:i', $ts) : $dt;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<title>แดชบอร์ดผู้ดูแลระบบ | นามหงส์ กรุ๊ป</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <div class="brand">นามหงส์ กรุ๊ป · แดชบอร์ดผู้ดูแลระบบ</div>
    <div>
      <span class="muted">เข้าสู่ระบบในชื่อ <?= h($_SESSION['admin_username'] ?? '') ?></span>
      &nbsp;·&nbsp; <a href="logout.php">ออกจากระบบ</a>
    </div>
  </div>

  <div class="tabs">
    <button class="tab <?= $activeTab === 'contact' ? 'active' : '' ?>" onclick="showTab('contact')">
      ข้อความติดต่อ <?php if ($unreadContacts): ?><span class="pill"><?= (int)$unreadContacts ?> ใหม่</span><?php endif; ?>
    </button>
    <button class="tab <?= $activeTab === 'jobs' ? 'active' : '' ?>" onclick="showTab('jobs')">
      ใบสมัครงาน <?php if ($unreadJobs): ?><span class="pill"><?= (int)$unreadJobs ?> ใหม่</span><?php endif; ?>
    </button>
  </div>

  <div id="panel-contact" style="<?= $activeTab === 'contact' ? '' : 'display:none;' ?>">
    <?php if (!$contacts): ?>
      <div class="card empty">ยังไม่มีข้อความติดต่อเข้ามา</div>
    <?php else: ?>
      <div class="card" style="padding:0;overflow-x:auto;">
        <table>
          <tr><th>วันที่</th><th>ชื่อ</th><th>ติดต่อกลับ</th><th>เรื่อง</th><th>ข้อความ</th><th></th></tr>
          <?php foreach ($contacts as $c): ?>
          <tr class="<?= $c['is_read'] ? '' : 'unread' ?>">
            <td><?= h(fmt_date($c['created_at'])) ?></td>
            <td><?= h($c['fullname']) ?><?php if ($c['company']): ?><br><span class="muted"><?= h($c['company']) ?></span><?php endif; ?></td>
            <td><a href="mailto:<?= h($c['email']) ?>"><?= h($c['email']) ?></a><br><?= h($c['phone']) ?></td>
            <td><?= h($c['subject']) ?></td>
            <td class="msg-cell"><?= h($c['message']) ?></td>
            <td>
              <div class="row-actions">
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= h($token) ?>">
                  <input type="hidden" name="table" value="contact_messages">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <input type="hidden" name="do" value="<?= $c['is_read'] ? 'unread' : 'read' ?>">
                  <button class="btn btn-outline btn-sm" type="submit"><?= $c['is_read'] ? 'ยังไม่อ่าน' : 'อ่านแล้ว' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('ลบข้อความนี้ถาวร?');">
                  <input type="hidden" name="csrf" value="<?= h($token) ?>">
                  <input type="hidden" name="table" value="contact_messages">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <input type="hidden" name="do" value="delete">
                  <button class="btn btn-danger btn-sm" type="submit">ลบ</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div id="panel-jobs" style="<?= $activeTab === 'jobs' ? '' : 'display:none;' ?>">
    <?php if (!$jobs): ?>
      <div class="card empty">ยังไม่มีใบสมัครงานเข้ามา</div>
    <?php else: ?>
      <div class="card" style="padding:0;overflow-x:auto;">
        <table>
          <tr><th>วันที่</th><th>ชื่อ</th><th>ติดต่อกลับ</th><th>ตำแหน่ง</th><th>ข้อความ</th><th></th></tr>
          <?php foreach ($jobs as $j): ?>
          <tr class="<?= $j['is_read'] ? '' : 'unread' ?>">
            <td><?= h(fmt_date($j['created_at'])) ?></td>
            <td><?= h($j['fullname']) ?></td>
            <td><a href="mailto:<?= h($j['email']) ?>"><?= h($j['email']) ?></a><br><?= h($j['phone']) ?></td>
            <td><?= h($j['position']) ?></td>
            <td class="msg-cell"><?= h($j['message'] ?? '') ?></td>
            <td>
              <div class="row-actions">
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= h($token) ?>">
                  <input type="hidden" name="table" value="job_applications">
                  <input type="hidden" name="id" value="<?= (int)$j['id'] ?>">
                  <input type="hidden" name="do" value="<?= $j['is_read'] ? 'unread' : 'read' ?>">
                  <button class="btn btn-outline btn-sm" type="submit"><?= $j['is_read'] ? 'ยังไม่อ่าน' : 'อ่านแล้ว' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('ลบใบสมัครนี้ถาวร?');">
                  <input type="hidden" name="csrf" value="<?= h($token) ?>">
                  <input type="hidden" name="table" value="job_applications">
                  <input type="hidden" name="id" value="<?= (int)$j['id'] ?>">
                  <input type="hidden" name="do" value="delete">
                  <button class="btn btn-danger btn-sm" type="submit">ลบ</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function showTab(name) {
  document.getElementById('panel-contact').style.display = name === 'contact' ? '' : 'none';
  document.getElementById('panel-jobs').style.display = name === 'jobs' ? '' : 'none';
  document.querySelectorAll('.tab').forEach(function (t, i) {
    t.classList.toggle('active', (name === 'contact' && i === 0) || (name === 'jobs' && i === 1));
  });
  history.replaceState(null, '', '?tab=' + name);
}
</script>
</body>
</html>

<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();
requireAdmin();

// Mark as read
if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$_GET['read']]);
    header("Location: messages.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$_GET['delete']]);
    header("Location: messages.php");
    exit();
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family:'Poppins',sans-serif; }
    body { background:#f4f6fb; }
    .sidebar { background:linear-gradient(180deg,#1a1a2e,#0f3460); min-height:100vh; width:240px; position:fixed; top:0; left:0; padding:25px 0; }
    .sidebar .brand { color:white; font-size:1.05rem; font-weight:700; padding:0 20px 20px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:15px; }
    .sidebar .brand span { color:#e67e22; }
    .sidebar .menu-label { color:rgba(255,255,255,0.35); font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; padding:10px 20px 5px; }
    .sidebar a { display:block; color:rgba(255,255,255,0.75); padding:10px 20px; text-decoration:none; font-size:0.88rem; transition:all 0.2s; }
    .sidebar a:hover,.sidebar a.active { background:rgba(230,126,34,0.2); color:#e67e22; border-left:3px solid #e67e22; }
    .sidebar a i { width:20px; margin-right:10px; }
    .main-content { margin-left:240px; padding:25px; }
    .topbar { background:white; border-radius:12px; padding:14px 25px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.06); margin-bottom:25px; }
    .topbar h5 { margin:0; font-weight:700; color:#1a1a2e; font-size:1rem; }
    .card-box { background:white; border-radius:14px; padding:25px; box-shadow:0 2px 15px rgba(0,0,0,0.06); }
    .card-box h6 { font-weight:700; color:#1a1a2e; border-bottom:2px solid #f0f0f0; padding-bottom:12px; margin-bottom:20px; }
    .msg-row { background:#fff8f0; border-left:3px solid #e67e22; }
    .msg-row.read { background:white; border-left:3px solid #e0e0e0; }
    .table { font-size:0.84rem; }
    .table thead th { color:#888; font-weight:600; font-size:0.75rem; text-transform:uppercase; background:#f8f9fa; border:none; }
    .table td { vertical-align:middle; }
    .msg-text { max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#555; font-size:0.82rem; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="brand"><span>Anniyappa</span> Admin</div>
  <div class="menu-label">Main</div>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
  <div class="menu-label">Manage</div>
  <a href="users.php"><i class="fas fa-users"></i>Students</a>
  <a href="internships.php"><i class="fas fa-briefcase"></i>Internships</a>
  <a href="registrations.php"><i class="fas fa-clipboard-list"></i>Registrations</a>
  <a href="books.php"><i class="fas fa-book"></i>Books</a>
  <a href="messages.php" class="active"><i class="fas fa-envelope"></i>Messages</a>
  <a href="gallery.php"><i class="fas fa-images"></i>Gallery</a>
  <div class="menu-label">System</div>
  <a href="../index.html"><i class="fas fa-globe"></i>View Website</a>
  <a href="../php/logout.php" style="color:#ff6b6b;"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>

<div class="main-content">
  <div class="topbar">
    <h5><i class="fas fa-envelope me-2" style="color:#e67e22;"></i>Contact Messages</h5>
    <a href="../php/logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
  </div>

  <div class="card-box">
    <h6><i class="fas fa-inbox me-2" style="color:#e67e22;"></i>All Messages (<?= count($messages) ?>)</h6>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>From</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($messages)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No messages yet.</td></tr>
        <?php else: ?>
        <?php foreach ($messages as $m): ?>
        <tr class="<?= $m['is_read'] ? 'read' : 'msg-row' ?>">
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($m['name']) ?></div>
            <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($m['email']) ?></div>
          </td>
          <td class="fw-semibold"><?= htmlspecialchars($m['subject'] ?? '—') ?></td>
          <td><div class="msg-text"><?= htmlspecialchars($m['message']) ?></div></td>
          <td style="font-size:0.8rem;"><?= date('d M Y, h:i A', strtotime($m['submitted_at'])) ?></td>
          <td>
            <?php if (!$m['is_read']): ?>
            <a href="messages.php?read=<?= $m['id'] ?>"
               class="btn btn-sm btn-outline-success rounded-pill me-1" title="Mark as Read">
              <i class="fas fa-check"></i>
            </a>
            <?php endif; ?>
            <a href="messages.php?delete=<?= $m['id'] ?>"
               class="btn btn-sm btn-outline-danger rounded-pill"
               onclick="return confirm('Delete this message?')" title="Delete">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
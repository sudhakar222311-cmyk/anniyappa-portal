<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();
requireAdmin();

// Delete user
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete']]);
    header("Location: users.php");
    exit();
}

$users = $pdo->query("SELECT * FROM users WHERE role='student' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Students | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: #f4f6fb; }
    .sidebar { background: linear-gradient(180deg,#1a1a2e,#0f3460); min-height:100vh; width:240px; position:fixed; top:0; left:0; padding:25px 0; }
    .sidebar .brand { color:white; font-size:1.05rem; font-weight:700; padding:0 20px 20px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:15px; }
    .sidebar .brand span { color:#e67e22; }
    .sidebar .menu-label { color:rgba(255,255,255,0.35); font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; padding:10px 20px 5px; }
    .sidebar a { display:block; color:rgba(255,255,255,0.75); padding:10px 20px; text-decoration:none; font-size:0.88rem; transition:all 0.2s; }
    .sidebar a:hover, .sidebar a.active { background:rgba(230,126,34,0.2); color:#e67e22; border-left:3px solid #e67e22; }
    .sidebar a i { width:20px; margin-right:10px; }
    .main-content { margin-left:240px; padding:25px; }
    .topbar { background:white; border-radius:12px; padding:14px 25px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.06); margin-bottom:25px; }
    .topbar h5 { margin:0; font-weight:700; color:#1a1a2e; font-size:1rem; }
    .data-card { background:white; border-radius:14px; padding:22px; box-shadow:0 2px 15px rgba(0,0,0,0.06); }
    .table { font-size:0.85rem; }
    .table thead th { color:#888; font-weight:600; border:none; font-size:0.78rem; text-transform:uppercase; background:#f8f9fa; }
    .table td { vertical-align:middle; }
    .avatar { width:35px; height:35px; border-radius:50%; background:#e67e22; color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="brand"><span>Anniyappa</span> Admin</div>
  <div class="menu-label">Main</div>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
  <div class="menu-label">Manage</div>
  <a href="users.php" class="active"><i class="fas fa-users"></i>Students</a>
  <a href="internships.php"><i class="fas fa-briefcase"></i>Internships</a>
  <a href="registrations.php"><i class="fas fa-clipboard-list"></i>Registrations</a>
  <a href="books.php"><i class="fas fa-book"></i>Books</a>
  <a href="messages.php"><i class="fas fa-envelope"></i>Messages</a>
  <a href="gallery.php"><i class="fas fa-images"></i>Gallery</a>
  <div class="menu-label">System</div>
  <a href="../index.html"><i class="fas fa-globe"></i>View Website</a>
  <a href="../php/logout.php" style="color:#ff6b6b;"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>

<div class="main-content">
  <div class="topbar">
    <h5><i class="fas fa-users me-2" style="color:#e67e22;"></i>Student Management</h5>
    <a href="../php/logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
  </div>

  <div class="data-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold mb-0">All Registered Students (<?= count($users) ?>)</h6>
    </div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Student</th>
          <th>Phone</th>
          <th>College</th>
          <th>Department</th>
          <th>Joined</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No students registered yet.</td></tr>
        <?php else: ?>
        <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
              <div>
                <div class="fw-semibold"><?= htmlspecialchars($user['full_name']) ?></div>
                <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($user['email']) ?></div>
              </div>
            </div>
          </td>
          <td><?= htmlspecialchars($user['phone'] ?? '—') ?></td>
          <td><?= htmlspecialchars($user['college'] ?? '—') ?></td>
          <td><?= htmlspecialchars($user['department'] ?? '—') ?></td>
          <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
          <td>
            <a href="users.php?delete=<?= $user['id'] ?>"
               class="btn btn-sm btn-outline-danger rounded-pill"
               onclick="return confirm('Delete this student?')">
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
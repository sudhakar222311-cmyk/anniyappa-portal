<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();
requireAdmin();

// Approve / Reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';
    $pdo->prepare("UPDATE internship_registrations SET status=? WHERE id=?")->execute([$action, $_GET['id']]);
    header("Location: registrations.php");
    exit();
}

$registrations = $pdo->query("
    SELECT ir.*, u.full_name, u.email, u.college, u.phone, i.title as internship_title, i.domain
    FROM internship_registrations ir
    JOIN users u ON ir.user_id = u.id
    JOIN internships i ON ir.internship_id = i.id
    ORDER BY ir.registration_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrations | Admin</title>
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
    .table { font-size:0.84rem; }
    .table thead th { color:#888; font-weight:600; font-size:0.75rem; text-transform:uppercase; background:#f8f9fa; border:none; }
    .table td { vertical-align:middle; }
    .badge-pending { background:#fff3cd; color:#856404; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:600; }
    .badge-approved { background:#d1e7dd; color:#0a3622; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:600; }
    .badge-rejected { background:#f8d7da; color:#842029; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:600; }
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
  <a href="registrations.php" class="active"><i class="fas fa-clipboard-list"></i>Registrations</a>
  <a href="books.php"><i class="fas fa-book"></i>Books</a>
  <a href="messages.php"><i class="fas fa-envelope"></i>Messages</a>
  <a href="gallery.php"><i class="fas fa-images"></i>Gallery</a>
  <div class="menu-label">System</div>
  <a href="../index.html"><i class="fas fa-globe"></i>View Website</a>
  <a href="../php/logout.php" style="color:#ff6b6b;"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>

<div class="main-content">
  <div class="topbar">
    <h5><i class="fas fa-clipboard-list me-2" style="color:#e67e22;"></i>Internship Registrations</h5>
    <a href="../php/logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
  </div>

  <div class="card-box">
    <h6><i class="fas fa-list me-2" style="color:#e67e22;"></i>All Registrations (<?= count($registrations) ?>)</h6>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Student</th>
          <th>Internship</th>
          <th>College</th>
          <th>Phone</th>
          <th>Applied On</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($registrations)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No registrations yet.</td></tr>
        <?php else: ?>
        <?php foreach ($registrations as $r): ?>
        <tr>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['full_name']) ?></div>
            <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($r['email']) ?></div>
          </td>
          <td>
            <div class="fw-semibold"><?= htmlspecialchars($r['internship_title']) ?></div>
            <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($r['domain']) ?></div>
          </td>
          <td><?= htmlspecialchars($r['college'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['phone'] ?? '—') ?></td>
          <td><?= date('d M Y', strtotime($r['registration_date'])) ?></td>
          <td><span class="badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
          <td>
            <?php if ($r['status'] === 'pending'): ?>
            <a href="registrations.php?action=approve&id=<?= $r['id'] ?>"
               class="btn btn-sm btn-success rounded-pill me-1"
               onclick="return confirm('Approve this registration?')">
              <i class="fas fa-check"></i>
            </a>
            <a href="registrations.php?action=reject&id=<?= $r['id'] ?>"
               class="btn btn-sm btn-danger rounded-pill"
               onclick="return confirm('Reject this registration?')">
              <i class="fas fa-times"></i>
            </a>
            <?php else: ?>
              <span class="text-muted" style="font-size:0.8rem;">Done</span>
            <?php endif; ?>
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
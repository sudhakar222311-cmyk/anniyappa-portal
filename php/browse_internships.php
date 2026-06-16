<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get internships with registration status for this user
$internships = $pdo->prepare("
    SELECT i.*, ir.status as my_status
    FROM internships i
    LEFT JOIN internship_registrations ir ON i.id = ir.internship_id AND ir.user_id = ?
    WHERE i.is_active = 1
    ORDER BY i.created_at DESC
");
$internships->execute([$user_id]);
$internships = $internships->fetchAll();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Browse Internships | Anniyappa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family:'Poppins',sans-serif; }
    body { background:#f4f6fb; }
    .sidebar { background:linear-gradient(180deg,#1a1a2e,#0f3460); min-height:100vh; width:240px; position:fixed; top:0; left:0; padding:30px 0; }
    .sidebar .brand { color:white; font-size:1.1rem; font-weight:700; padding:0 20px 25px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:20px; }
    .sidebar .brand span { color:#e67e22; }
    .sidebar a { display:block; color:rgba(255,255,255,0.75); padding:11px 20px; text-decoration:none; font-size:0.9rem; }
    .sidebar a:hover,.sidebar a.active { background:rgba(230,126,34,0.2); color:#e67e22; border-left:3px solid #e67e22; }
    .sidebar a i { width:20px; margin-right:10px; }
    .main-content { margin-left:240px; padding:30px; }
    .topbar { background:white; border-radius:12px; padding:15px 25px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.06); margin-bottom:25px; }
    .topbar h5 { margin:0; font-weight:700; color:#1a1a2e; }
    .intern-card { background:white; border-radius:16px; padding:25px; box-shadow:0 2px 15px rgba(0,0,0,0.06); height:100%; border-top:4px solid #e67e22; }
    .intern-card h5 { font-weight:700; color:#1a1a2e; }
    .intern-card .meta { font-size:0.82rem; color:#888; margin-bottom:4px; }
    .intern-card .meta i { width:16px; color:#e67e22; }
    .btn-reg { background:#e67e22; color:white; border:none; border-radius:20px; padding:8px 22px; font-size:0.85rem; font-weight:600; }
    .btn-reg:hover { background:#cf6d17; color:white; }
    .status-pending { background:#fff3cd; color:#856404; border-radius:20px; padding:6px 16px; font-size:0.82rem; font-weight:600; }
    .status-approved { background:#d1e7dd; color:#0a3622; border-radius:20px; padding:6px 16px; font-size:0.82rem; font-weight:600; }
    .status-rejected { background:#f8d7da; color:#842029; border-radius:20px; padding:6px 16px; font-size:0.82rem; font-weight:600; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="brand"><span>Anniyappa</span> Portal</div>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="browse_internships.php" class="active"><i class="fas fa-briefcase"></i> Internships</a>
  <a href="../pages/bookshelf.html"><i class="fas fa-book"></i> Books</a>
  <a href="../pages/gallery.html"><i class="fas fa-images"></i> Gallery</a>
  <a href="../pages/contact.html"><i class="fas fa-envelope"></i> Contact</a>
  <a href="../index.html"><i class="fas fa-globe"></i> Main Website</a>
  <a href="logout.php" style="color:#ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
  <div class="topbar">
    <h5>Browse Internships</h5>
    <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-info alert-dismissible fade show"><?= htmlspecialchars($flash) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <?php if (empty($internships)): ?>
      <p class="text-muted text-center py-5">No internships available right now. Check back soon!</p>
    <?php else: ?>
    <?php foreach ($internships as $i): ?>
    <div class="col-md-6 col-lg-4">
      <div class="intern-card">
        <h5><?= htmlspecialchars($i['title']) ?></h5>
        <div class="meta"><i class="fas fa-layer-group"></i> <?= htmlspecialchars($i['domain']) ?></div>
        <div class="meta"><i class="fas fa-clock"></i> <?= htmlspecialchars($i['duration'] ?? 'N/A') ?></div>
        <div class="meta"><i class="fas fa-laptop-house"></i> <?= ucfirst($i['mode']) ?></div>
        <div class="meta"><i class="fas fa-users"></i> <?= $i['seats'] ?> Seats</div>
        <p class="text-muted mt-2" style="font-size:0.85rem;"><?= htmlspecialchars(substr($i['description'] ?? '', 0, 100)) ?>...</p>

        <?php if ($i['my_status']): ?>
          <span class="status-<?= $i['my_status'] ?>"><?= ucfirst($i['my_status']) ?></span>
        <?php else: ?>
          <form method="POST" action="register_internship.php">
            <input type="hidden" name="internship_id" value="<?= $i['id'] ?>"/>
            <button type="submit" class="btn-reg mt-2">
              <i class="fas fa-paper-plane me-1"></i>Register Now
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
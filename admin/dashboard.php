<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();
requireAdmin();

// Fetch counts for stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalInternships = $pdo->query("SELECT COUNT(*) FROM internships")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
$totalRegistrations = $pdo->query("SELECT COUNT(*) FROM internship_registrations")->fetchColumn();

// Recent registrations
$recentRegs = $pdo->query("
    SELECT u.full_name, u.email, u.college, ir.registration_date, ir.status
    FROM internship_registrations ir
    JOIN users u ON ir.user_id = u.id
    ORDER BY ir.registration_date DESC
    LIMIT 5
")->fetchAll();

// Recent messages
$recentMsgs = $pdo->query("
    SELECT name, email, subject, submitted_at
    FROM contact_messages
    ORDER BY submitted_at DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard | Anniyappa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: #f4f6fb; }
    .sidebar {
      background: linear-gradient(180deg, #1a1a2e, #0f3460);
      min-height: 100vh; width: 240px;
      position: fixed; top: 0; left: 0;
      padding: 25px 0;
    }
    .sidebar .brand {
      color: white; font-size: 1.05rem; font-weight: 700;
      padding: 0 20px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin-bottom: 15px;
    }
    .sidebar .brand span { color: #e67e22; }
    .sidebar .menu-label {
      color: rgba(255,255,255,0.35);
      font-size: 0.7rem; text-transform: uppercase;
      letter-spacing: 1px; padding: 10px 20px 5px;
    }
    .sidebar a {
      display: block; color: rgba(255,255,255,0.75);
      padding: 10px 20px; text-decoration: none;
      font-size: 0.88rem; transition: all 0.2s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(230,126,34,0.2);
      color: #e67e22;
      border-left: 3px solid #e67e22;
    }
    .sidebar a i { width: 20px; margin-right: 10px; }
    .main-content { margin-left: 240px; padding: 25px; }
    .topbar {
      background: white; border-radius: 12px;
      padding: 14px 25px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      margin-bottom: 25px;
    }
    .topbar h5 { margin: 0; font-weight: 700; color: #1a1a2e; font-size: 1rem; }
    .admin-badge {
      background: #e67e22; color: white;
      border-radius: 20px; padding: 4px 14px;
      font-size: 0.8rem; font-weight: 600;
    }
    /* Stat Cards */
    .stat-card {
      background: white; border-radius: 14px;
      padding: 22px; box-shadow: 0 2px 15px rgba(0,0,0,0.06);
      display: flex; justify-content: space-between; align-items: center;
      border-left: 4px solid #e67e22;
      transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card h3 { font-weight: 700; color: #1a1a2e; margin: 0; font-size: 1.8rem; }
    .stat-card p { color: #888; margin: 0; font-size: 0.82rem; }
    .stat-card .icon { font-size: 2rem; opacity: 0.2; }
    /* Tables */
    .data-card {
      background: white; border-radius: 14px;
      padding: 22px; box-shadow: 0 2px 15px rgba(0,0,0,0.06);
    }
    .data-card h6 {
      font-weight: 700; color: #1a1a2e;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 12px; margin-bottom: 15px;
    }
    .table { font-size: 0.85rem; }
    .table thead th { color: #888; font-weight: 600; border: none; font-size: 0.78rem; text-transform: uppercase; }
    .table td { vertical-align: middle; color: #444; }
    .badge-pending { background: #fff3cd; color: #856404; border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; }
    .badge-approved { background: #d1e7dd; color: #0a3622; border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; }
    .badge-rejected { background: #f8d7da; color: #842029; border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; }
    /* Welcome Banner */
    .welcome-banner {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: white; border-radius: 16px;
      padding: 25px 30px; margin-bottom: 25px;
      display: flex; justify-content: space-between; align-items: center;
    }
    .welcome-banner h4 { font-weight: 700; margin: 0 0 5px; }
    .welcome-banner p { color: #a0aec0; margin: 0; font-size: 0.88rem; }
    .quick-btn {
      background: rgba(255,255,255,0.1);
      color: white; border: 1px solid rgba(255,255,255,0.2);
      border-radius: 20px; padding: 6px 18px;
      font-size: 0.85rem; text-decoration: none;
      transition: all 0.2s;
    }
    .quick-btn:hover { background: #e67e22; border-color: #e67e22; color: white; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="brand"><span>Anniyappa</span> Admin</div>

  <div class="menu-label">Main</div>
  <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a>

  <div class="menu-label">Manage</div>
  <a href="users.php"><i class="fas fa-users"></i>Students</a>
  <a href="internships.php"><i class="fas fa-briefcase"></i>Internships</a>
  <a href="registrations.php"><i class="fas fa-clipboard-list"></i>Registrations</a>
  <a href="books.php"><i class="fas fa-book"></i>Books</a>
  <a href="messages.php"><i class="fas fa-envelope"></i>Messages
    <?php if ($totalMessages > 0): ?>
      <span class="badge bg-danger ms-1" style="font-size:0.65rem;"><?= $totalMessages ?></span>
    <?php endif; ?>
  </a>
  <a href="gallery.php"><i class="fas fa-images"></i>Gallery</a>

  <div class="menu-label">System</div>
  <a href="../index.html"><i class="fas fa-globe"></i>View Website</a>
  <a href="../php/logout.php" style="color:#ff6b6b;"><i class="fas fa-sign-out-alt"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <h5><i class="fas fa-tachometer-alt me-2" style="color:#e67e22;"></i>Admin Dashboard</h5>
    <div class="d-flex align-items-center gap-3">
      <span class="admin-badge"><i class="fas fa-shield-alt me-1"></i>Admin</span>
      <span style="font-size:0.85rem; color:#888;"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
      <a href="../php/logout.php" class="btn btn-sm btn-outline-danger rounded-pill">
        <i class="fas fa-sign-out-alt me-1"></i>Logout
      </a>
    </div>
  </div>

  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div>
      <h4>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?>! 👋</h4>
      <p>Here's what's happening on the portal today.</p>
    </div>
    <a href="../index.html" class="quick-btn">
      <i class="fas fa-external-link-alt me-1"></i>View Live Site
    </a>
  </div>

  <!-- Stats Row -->
  <div class="row g-4 mb-4">
    <div class="col-md-4 col-lg-2-4">
      <div class="stat-card" style="border-left-color:#e67e22;">
        <div>
          <h3><?= $totalUsers ?></h3>
          <p>Total Students</p>
        </div>
        <i class="fas fa-users icon" style="color:#e67e22;"></i>
      </div>
    </div>
    <div class="col-md-4 col-lg-2-4">
      <div class="stat-card" style="border-left-color:#3498db;">
        <div>
          <h3><?= $totalRegistrations ?></h3>
          <p>Registrations</p>
        </div>
        <i class="fas fa-clipboard-list icon" style="color:#3498db;"></i>
      </div>
    </div>
    <div class="col-md-4 col-lg-2-4">
      <div class="stat-card" style="border-left-color:#2ecc71;">
        <div>
          <h3><?= $totalInternships ?></h3>
          <p>Internships</p>
        </div>
        <i class="fas fa-briefcase icon" style="color:#2ecc71;"></i>
      </div>
    </div>
    <div class="col-md-6 col-lg-2-4">
      <div class="stat-card" style="border-left-color:#9b59b6;">
        <div>
          <h3><?= $totalBooks ?></h3>
          <p>Books Listed</p>
        </div>
        <i class="fas fa-book icon" style="color:#9b59b6;"></i>
      </div>
    </div>
    <div class="col-md-6 col-lg-2-4">
      <div class="stat-card" style="border-left-color:#e74c3c;">
        <div>
          <h3><?= $totalMessages ?></h3>
          <p>Unread Messages</p>
        </div>
        <i class="fas fa-envelope icon" style="color:#e74c3c;"></i>
      </div>
    </div>
  </div>

  <!-- Tables Row -->
  <div class="row g-4">

    <!-- Recent Registrations -->
    <div class="col-lg-7">
      <div class="data-card">
        <h6><i class="fas fa-clipboard-list me-2" style="color:#e67e22;"></i>Recent Internship Registrations</h6>
        <?php if (empty($recentRegs)): ?>
          <p class="text-muted text-center py-3" style="font-size:0.88rem;">No registrations yet.</p>
        <?php else: ?>
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Student</th>
              <th>College</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentRegs as $reg): ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars($reg['full_name']) ?></div>
                <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($reg['email']) ?></div>
              </td>
              <td><?= htmlspecialchars($reg['college'] ?? '—') ?></td>
              <td><?= date('d M Y', strtotime($reg['registration_date'])) ?></td>
              <td>
                <span class="badge-<?= $reg['status'] ?>"><?= ucfirst($reg['status']) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
        <div class="mt-3">
          <a href="registrations.php" class="btn btn-sm btn-outline-warning rounded-pill">
            View All <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-lg-5">
      <div class="data-card">
        <h6><i class="fas fa-envelope me-2" style="color:#e67e22;"></i>Recent Messages</h6>
        <?php if (empty($recentMsgs)): ?>
          <p class="text-muted text-center py-3" style="font-size:0.88rem;">No messages yet.</p>
        <?php else: ?>
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>From</th>
              <th>Subject</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentMsgs as $msg): ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars($msg['name']) ?></div>
                <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($msg['email']) ?></div>
              </td>
              <td style="font-size:0.82rem;"><?= htmlspecialchars($msg['subject'] ?? '—') ?></td>
              <td style="font-size:0.8rem;"><?= date('d M', strtotime($msg['submitted_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
        <div class="mt-3">
          <a href="messages.php" class="btn btn-sm btn-outline-warning rounded-pill">
            View All <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
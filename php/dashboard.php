<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();

if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Dashboard | Anniyappa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: #f4f6fb; }
    .sidebar { background: linear-gradient(180deg, #1a1a2e, #0f3460); min-height: 100vh; padding: 30px 0; width: 240px; position: fixed; top: 0; left: 0; }
    .sidebar .brand { color: white; font-size: 1.1rem; font-weight: 700; padding: 0 20px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
    .sidebar .brand span { color: #e67e22; }
    .sidebar a { display: block; color: rgba(255,255,255,0.75); padding: 11px 20px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
    .sidebar a:hover, .sidebar a.active { background: rgba(230,126,34,0.2); color: #e67e22; border-left: 3px solid #e67e22; }
    .sidebar a i { width: 20px; margin-right: 10px; }
    .main-content { margin-left: 240px; padding: 30px; }
    .topbar { background: white; border-radius: 12px; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 25px; }
    .topbar h5 { margin: 0; font-weight: 700; color: #1a1a2e; }
    .stat-card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.06); border-left: 4px solid #e67e22; }
    .stat-card h3 { font-weight: 700; color: #1a1a2e; margin: 0; }
    .stat-card p { color: #888; margin: 0; font-size: 0.88rem; }
    .stat-card .icon { font-size: 2rem; color: #e67e22; opacity: 0.3; }
    .welcome-card { background: linear-gradient(135deg, #1a1a2e, #0f3460); color: white; border-radius: 16px; padding: 30px; margin-bottom: 25px; }
    .welcome-card h4 { font-weight: 700; }
    .welcome-card p { color: #a0aec0; margin: 0; }
    .btn-logout { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 20px; padding: 5px 15px; font-size: 0.85rem; }
    .btn-logout:hover { background: #e67e22; border-color: #e67e22; color: white; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="brand"><span>Anniyappa</span> Portal</div>
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="browse_internships.php"><i class="fas fa-graduation-cap"></i> My Internship</a>
    <a href="#"><i class="fas fa-book"></i> Books</a>
    <a href="#"><i class="fas fa-images"></i> Gallery</a>
    <a href="../pages/contact.html"><i class="fas fa-envelope"></i> Contact</a>
    <a href="../index.html"><i class="fas fa-globe"></i> Main Website</a>
    <a href="logout.php" style="margin-top:auto; color:#ff6b6b;">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="topbar">
      <h5>Student Dashboard</h5>
      <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill">
        <i class="fas fa-sign-out-alt me-1"></i>Logout
      </a>
    </div>

    <!-- Welcome -->
    <div class="welcome-card">
      <h4>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>! 👋</h4>
      <p>You are logged in as a Student. Explore your internship programs and resources below.</p>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
          <div>
            <h3>0</h3>
            <p>Registered Internships</p>
          </div>
          <i class="fas fa-briefcase icon"></i>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card d-flex justify-content-between align-items-center" style="border-left-color:#3498db;">
          <div>
            <h3>0</h3>
            <p>Certificates Earned</p>
          </div>
          <i class="fas fa-certificate icon" style="color:#3498db;"></i>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card d-flex justify-content-between align-items-center" style="border-left-color:#2ecc71;">
          <div>
            <h3>0</h3>
            <p>Attendance %</p>
          </div>
          <i class="fas fa-calendar-check icon" style="color:#2ecc71;"></i>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card d-flex justify-content-between align-items-center" style="border-left-color:#9b59b6;">
          <div>
            <h3>0</h3>
            <p>Assignments Done</p>
          </div>
          <i class="fas fa-tasks icon" style="color:#9b59b6;"></i>
        </div>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-3 p-4 shadow-sm">
      <h6 class="fw-bold mb-3" style="color:#1a1a2e;">Quick Actions</h6>
      <a href="../pages/internship.html" class="btn btn-warning me-2 mb-2 rounded-pill">
        <i class="fas fa-plus me-1"></i>Browse Internships
      </a>
      <a href="../pages/bookshelf.html" class="btn btn-outline-dark me-2 mb-2 rounded-pill">
        <i class="fas fa-book me-1"></i>View Books
      </a>
      <a href="../pages/contact.html" class="btn btn-outline-secondary mb-2 rounded-pill">
        <i class="fas fa-envelope me-1"></i>Contact Us
      </a>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
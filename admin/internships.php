<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();
requireAdmin();

$success = "";
$error = "";

// Add new internship
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $domain      = trim($_POST['domain']);
    $duration    = trim($_POST['duration']);
    $mode        = $_POST['mode'];
    $seats       = intval($_POST['seats']);
    $start_date  = $_POST['start_date'];
    $end_date    = $_POST['end_date'];
    $description = trim($_POST['description']);

    if (empty($title) || empty($domain)) {
        $error = "Title and Domain are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO internships 
            (title, domain, duration, mode, seats, start_date, end_date, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $domain, $duration, $mode, $seats, $start_date, $end_date, $description]);
        $success = "Internship added successfully!";
    }
}

// Delete internship
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM internships WHERE id=?")->execute([$_GET['delete']]);
    header("Location: internships.php");
    exit();
}

// Toggle active status
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE internships SET is_active = !is_active WHERE id=?");
    $stmt->execute([$_GET['toggle']]);
    header("Location: internships.php");
    exit();
}

$internships = $pdo->query("SELECT * FROM internships ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Internships | Admin</title>
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
    .card-box { background:white; border-radius:14px; padding:25px; box-shadow:0 2px 15px rgba(0,0,0,0.06); margin-bottom:25px; }
    .card-box h6 { font-weight:700; color:#1a1a2e; border-bottom:2px solid #f0f0f0; padding-bottom:12px; margin-bottom:20px; }
    .form-control,.form-select { border-radius:8px; font-size:0.88rem; border:1.5px solid #e0e0e0; padding:9px 12px; }
    .form-control:focus,.form-select:focus { border-color:#e67e22; box-shadow:0 0 0 3px rgba(230,126,34,0.15); }
    .form-label { font-size:0.82rem; font-weight:600; color:#555; }
    .btn-add { background:#e67e22; color:white; border:none; border-radius:8px; padding:10px 25px; font-weight:600; font-size:0.9rem; }
    .btn-add:hover { background:#cf6d17; color:white; }
    .table { font-size:0.84rem; }
    .table thead th { color:#888; font-weight:600; font-size:0.75rem; text-transform:uppercase; background:#f8f9fa; border:none; }
    .table td { vertical-align:middle; }
    .badge-active { background:#d1e7dd; color:#0a3622; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:600; }
    .badge-inactive { background:#f8d7da; color:#842029; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:600; }
    .mode-badge { background:#e8f4ff; color:#0066cc; border-radius:20px; padding:3px 10px; font-size:0.75rem; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="brand"><span>Anniyappa</span> Admin</div>
  <div class="menu-label">Main</div>
  <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
  <div class="menu-label">Manage</div>
  <a href="users.php"><i class="fas fa-users"></i>Students</a>
  <a href="internships.php" class="active"><i class="fas fa-briefcase"></i>Internships</a>
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
    <h5><i class="fas fa-briefcase me-2" style="color:#e67e22;"></i>Internship Management</h5>
    <a href="../php/logout.php" class="btn btn-sm btn-outline-danger rounded-pill">Logout</a>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" style="font-size:0.88rem;">
      <?= $success ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" style="font-size:0.88rem;">
      <?= $error ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Add Form -->
  <div class="card-box">
    <h6><i class="fas fa-plus-circle me-2" style="color:#e67e22;"></i>Add New Internship</h6>
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Internship Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. AI & ML Summer Internship" required/>
        </div>
        <div class="col-md-6">
          <label class="form-label">Domain *</label>
          <input type="text" name="domain" class="form-control" placeholder="e.g. Artificial Intelligence"/>
        </div>
        <div class="col-md-4">
          <label class="form-label">Duration</label>
          <input type="text" name="duration" class="form-control" placeholder="e.g. 4 Weeks"/>
        </div>
        <div class="col-md-4">
          <label class="form-label">Mode</label>
          <select name="mode" class="form-select">
            <option value="offline">Offline</option>
            <option value="online">Online</option>
            <option value="hybrid">Hybrid</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Total Seats</label>
          <input type="number" name="seats" class="form-control" value="30" min="1"/>
        </div>
        <div class="col-md-6">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-control"/>
        </div>
        <div class="col-md-6">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" class="form-control"/>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the internship..."></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn-add">
            <i class="fas fa-plus me-2"></i>Add Internship
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Internship List -->
  <div class="card-box">
    <h6><i class="fas fa-list me-2" style="color:#e67e22;"></i>All Internships (<?= count($internships) ?>)</h6>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Title</th>
          <th>Domain</th>
          <th>Duration</th>
          <th>Mode</th>
          <th>Seats</th>
          <th>Dates</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($internships)): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No internships added yet.</td></tr>
        <?php else: ?>
        <?php foreach ($internships as $i): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($i['title']) ?></td>
          <td><?= htmlspecialchars($i['domain']) ?></td>
          <td><?= htmlspecialchars($i['duration'] ?? '—') ?></td>
          <td><span class="mode-badge"><?= ucfirst($i['mode']) ?></span></td>
          <td><?= $i['seats'] ?></td>
          <td style="font-size:0.78rem;">
            <?= $i['start_date'] ? date('d M Y', strtotime($i['start_date'])) : '—' ?>
            <?= $i['end_date'] ? ' → '.date('d M Y', strtotime($i['end_date'])) : '' ?>
          </td>
          <td>
            <span class="<?= $i['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
              <?= $i['is_active'] ? 'Active' : 'Inactive' ?>
            </span>
          </td>
          <td>
            <a href="internships.php?toggle=<?= $i['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill me-1"
               title="Toggle Status">
              <i class="fas fa-toggle-on"></i>
            </a>
            <a href="internships.php?delete=<?= $i['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill"
               onclick="return confirm('Delete this internship?')" title="Delete">
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
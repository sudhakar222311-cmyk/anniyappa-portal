<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $password    = trim($_POST['password']);
    $confirm     = trim($_POST['confirm_password']);
    $phone       = trim($_POST['phone']);
    $college     = trim($_POST['college']);
    $department  = trim($_POST['department']);

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email is already registered.";
        } else {
            $hashed = md5($password);
            $stmt = $pdo->prepare("INSERT INTO users 
                (full_name, email, password, phone, college, department, role) 
                VALUES (?, ?, ?, ?, ?, ?, 'student')");
            $stmt->execute([$full_name, $email, $hashed, $phone, $college, $department]);
            $success = "Registration successful! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register | Anniyappa Publications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(135deg, #1a1a2e, #0f3460); min-height: 100vh; display: flex; align-items: center; padding: 40px 0; }
    .auth-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 520px; width: 100%; margin: 0 auto; }
    .auth-card h2 { font-weight: 700; color: #1a1a2e; margin-bottom: 5px; }
    .auth-card p.subtitle { color: #888; font-size: 0.9rem; margin-bottom: 25px; }
    .brand { color: #e67e22; font-weight: 700; }
    .form-control { border-radius: 10px; padding: 10px 15px; border: 1.5px solid #e0e0e0; font-size: 0.92rem; }
    .form-control:focus { border-color: #e67e22; box-shadow: 0 0 0 3px rgba(230,126,34,0.15); }
    .form-label { font-weight: 500; font-size: 0.88rem; color: #444; }
    .btn-register { background: #e67e22; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; font-size: 1rem; transition: all 0.3s; }
    .btn-register:hover { background: #cf6d17; color: white; }
    .divider { text-align: center; color: #aaa; font-size: 0.85rem; margin: 15px 0; }
    .login-link { text-align: center; font-size: 0.9rem; margin-top: 15px; }
    .login-link a { color: #e67e22; font-weight: 600; text-decoration: none; }
  </style>
</head>
<body>
<div class="container">
  <div class="auth-card">
    <div class="text-center mb-3">
      <a href="../index.html" style="text-decoration:none;">
        <span class="brand" style="font-size:1.3rem;">Anniyappa</span>
        <span style="font-size:1.3rem; color:#1a1a2e;"> Publications</span>
      </a>
    </div>
    <h2>Create Account</h2>
    <p class="subtitle">Join our internship & training ecosystem</p>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2" style="font-size:0.88rem;"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success py-2" style="font-size:0.88rem;"><?= $success ?>
        <a href="login.php" class="fw-bold">Login here</a>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Full Name *</label>
        <input type="text" name="full_name" class="form-control" placeholder="Your full name" required/>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address *</label>
        <input type="email" name="email" class="form-control" placeholder="your@email.com" required/>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required/>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-control" placeholder="+91 XXXXXXXXXX"/>
      </div>
      <div class="mb-3">
        <label class="form-label">College / Institution</label>
        <input type="text" name="college" class="form-control" placeholder="Your college name"/>
      </div>
      <div class="mb-3">
        <label class="form-label">Department</label>
        <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science"/>
      </div>
      <button type="submit" class="btn-register mt-2">
        <i class="fas fa-user-plus me-2"></i>Create Account
      </button>
    </form>
    <div class="login-link">Already have an account? <a href="login.php">Login here</a></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = md5(trim($_POST['password']));

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND is_active = 1");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['role']      = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Anniyappa Publications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(135deg, #1a1a2e, #0f3460); min-height: 100vh; display: flex; align-items: center; }
    .auth-card { background: white; border-radius: 20px; padding: 45px 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 420px; width: 100%; margin: 0 auto; }
    .auth-card h2 { font-weight: 700; color: #1a1a2e; margin-bottom: 5px; }
    .auth-card p.subtitle { color: #888; font-size: 0.9rem; margin-bottom: 25px; }
    .brand { color: #e67e22; font-weight: 700; }
    .form-control { border-radius: 10px; padding: 11px 15px; border: 1.5px solid #e0e0e0; font-size: 0.92rem; }
    .form-control:focus { border-color: #e67e22; box-shadow: 0 0 0 3px rgba(230,126,34,0.15); }
    .form-label { font-weight: 500; font-size: 0.88rem; color: #444; }
    .btn-login { background: #e67e22; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; font-size: 1rem; transition: all 0.3s; }
    .btn-login:hover { background: #cf6d17; color: white; }
    .register-link { text-align: center; font-size: 0.9rem; margin-top: 15px; }
    .register-link a { color: #e67e22; font-weight: 600; text-decoration: none; }
    .home-link { text-align: center; margin-top: 12px; font-size: 0.85rem; }
    .home-link a { color: #888; text-decoration: none; }
    .home-link a:hover { color: #e67e22; }
  </style>
</head>
<body>
<div class="container">
  <div class="auth-card">
    <div class="text-center mb-4">
      <a href="../index.html" style="text-decoration:none;">
        <span class="brand" style="font-size:1.3rem;">Anniyappa</span>
        <span style="font-size:1.3rem; color:#1a1a2e;"> Publications</span>
      </a>
    </div>
    <h2>Welcome Back</h2>
    <p class="subtitle">Login to your student portal account</p>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2" style="font-size:0.88rem;"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="your@email.com" required/>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Your password" required/>
      </div>
      <button type="submit" class="btn-login mt-1">
        <i class="fas fa-sign-in-alt me-2"></i>Login
      </button>
    </form>

    <div class="register-link mt-3">Don't have an account? <a href="register.php">Register here</a></div>
    <div class="home-link"><a href="../index.html"><i class="fas fa-home me-1"></i>Back to Home</a></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
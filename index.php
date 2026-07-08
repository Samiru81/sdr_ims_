<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND status='active' LIMIT 1");

    if ($query && mysqli_num_rows($query) === 1) {
        $userRow = mysqli_fetch_assoc($query);

        if (password_verify($password, $userRow['password'])) {
            $_SESSION['user_id'] = $userRow['id'];
            $_SESSION['name'] = $userRow['full_name'];
            $_SESSION['role'] = $userRow['role'];
            mysqli_query($conn, "UPDATE users SET last_login=NOW() WHERE id=" . (int)$userRow['id']);
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username or inactive account.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>
(function(){
  try{
    var saved = localStorage.getItem('sdr-ims-theme');
    var theme = saved || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) ? 'light' : 'dark');
    document.documentElement.setAttribute('data-theme', theme);
  }catch(e){document.documentElement.setAttribute('data-theme','dark');}
})();
</script>
<meta charset="UTF-8">
<title>SDR IMS Login</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css?v=online_ims_light_user_lowstock_1">
<script src="assets/js/password-security.js?v=1"></script>
<script src="assets/js/theme-toggle.js?v=1" defer></script>
</head>
<body class="login-body login-modern">
<div class="login-floating-theme">
  <button type="button" class="theme-toggle" onclick="toggleThemeMode()" aria-label="Toggle dark and light mode" title="Dark / Light Mode">
    <span class="theme-toggle-track">
      <span class="theme-toggle-stars"></span>
      <span class="theme-toggle-thumb">
        <i class="fa-solid fa-moon theme-icon theme-icon-moon"></i>
        <i class="fa-solid fa-sun theme-icon theme-icon-sun"></i>
      </span>
    </span>
    <span class="theme-toggle-text">Dark</span>
  </button>
</div>
<div class="login-bg-glow glow-one"></div>
<div class="login-bg-glow glow-two"></div>

<div class="login-shell">
    <div class="login-left-panel">
        <div class="login-brand-row">
            <img src="assets/img/sdr-ims-logo.png" alt="SDR IMS Logo">
            <div>
                <h1>SDR IMS</h1>
                <p>Inventory Management System</p>
            </div>
        </div>

        <div class="login-hero">
            <h2>Manage stock faster, smarter and easier.</h2>
            <p>Track products, barcode sales, low stock alerts, discounts and reports from one clean dashboard.</p>
        </div>

        <div class="login-feature-list">
            <span><i class="fa-solid fa-barcode"></i> Barcode Ready</span>
            <span><i class="fa-solid fa-triangle-exclamation"></i> Stock Alerts</span>
            <span><i class="fa-solid fa-chart-line"></i> Reports</span>
        </div>
    </div>

    <div class="login-card login-card-modern">
        <div class="login-card-top">
            <img class="login-logo-img" src="assets/img/sdr-ims-logo.png" alt="SDR IMS Logo">
            <h1 class="login-title">Welcome Back</h1>
            <p class="text-muted">Login to SDR IMS dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-banner alert-danger" style="margin-top:18px"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form-modern">
            <div class="fg login-input-wrap">
                <label>Username</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-user input-leading-icon"></i>
                    <input type="text" name="username" placeholder="Enter Username" required>
                </div>
            </div>
            <div class="fg login-input-wrap">
                <label>Password</label>
                <div class="password-box password-box-left-eye">
                    <button type="button" class="password-eye-btn-left" onclick="toggleLoginPassword()" aria-label="Show password">
                        <span id="passwordToggleIcon">👁</span>
                    </button>
                    <input data-secure-password="1" type="password" name="password" id="loginPassword" placeholder="Enter Password" required>
                </div>
            </div>
            <button class="btn-sm btn-primary login-btn-motion" name="login" type="submit" style="width:100%;padding:13px">
                <span>Login</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
            <div class="forgot-row">
                <a href="forgot_password.php"><i class="fa-solid fa-key"></i> Forgot Password?</a>
            </div>
        </form>

        
    </div>
</div>

<script>
function toggleLoginPassword() {
  const pass = document.getElementById('loginPassword');
  const icon = document.getElementById('passwordToggleIcon');
  if (!pass || !icon) return;

  if (pass.type === 'password') {
    pass.type = 'text';
    icon.textContent = '🙈';
  } else {
    pass.type = 'password';
    icon.textContent = '👁';
  }
}
</script>
</body>
</html>

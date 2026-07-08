<?php
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$err = "";

if (isset($_POST['reset_password'])) {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '') {
        $err = "Username and email are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $err = "Password confirmation does not match.";
    } elseif (!valid_password_rule($newPassword)) {
        $err = password_rule_message();
    } else {
        $res = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' AND email='$email' AND status='active' LIMIT 1");

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
            $uid = (int)$user['id'];
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            if (mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$uid")) {
                $msg = "Password changed successfully. You can login now.";
            } else {
                $err = "Password update failed.";
            }
        } else {
            $err = "No active user found with that username and email.";
        }
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
<title>Forgot Password - SDR IMS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css?v=online_ims_light_user_lowstock_1">
<script src="assets/js/password-security.js?v=1"></script>
<script src="assets/js/theme-toggle.js?v=1" defer></script>
</head>
<body class="login-body login-modern forgot-scroll-page">
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

<div class="forgot-shell">
    <div class="login-card login-card-modern forgot-card">
        <div class="login-card-top">
            <img class="login-logo-img" src="assets/img/sdr-ims-logo.png" alt="SDR IMS Logo">
            <h1 class="login-title">Reset Password</h1>
            <p class="text-muted">Enter your username and email to change your password.</p>
        </div>

        <?php if ($msg): ?>
            <div class="alert-banner alert-success" style="margin-top:18px"><?php echo h($msg); ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert-banner alert-danger" style="margin-top:18px"><?php echo h($err); ?></div>
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
                <label>Email</label>
                <div class="input-icon-wrap">
                    <i class="fa-solid fa-envelope input-leading-icon"></i>
                    <input type="email" name="email" placeholder="Enter Account Email" required>
                </div>
            </div>

            <div class="fg login-input-wrap">
                <label>New Password</label>
                <div class="password-box">
                    <span class="password-lock">🔒</span>
                    <input data-secure-password="1" type="password" name="new_password" id="newPassword" placeholder="Example: Abc@1" minlength="5" maxlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,8}" title="5-8 characters with letters, numbers and symbols" required>
                    <button type="button" class="password-eye-btn" onclick="toggleFieldPassword('newPassword','newPassIcon')" aria-label="Show password">
                        <span id="newPassIcon">👁</span>
                    </button>
                </div>
            </div>

            <div class="fg login-input-wrap">
                <label>Confirm Password</label>
                <div class="password-box">
                    <span class="password-lock">🔒</span>
                    <input data-secure-password="1" type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm New Password" minlength="5" maxlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,8}" title="5-8 characters with letters, numbers and symbols" required>
                    <button type="button" class="password-eye-btn" onclick="toggleFieldPassword('confirmPassword','confirmPassIcon')" aria-label="Show password">
                        <span id="confirmPassIcon">👁</span>
                    </button>
                </div>
            </div>

            <button class="btn-sm btn-primary login-btn-motion" name="reset_password" type="submit" style="width:100%;padding:13px">
                <span>Change Password</span>
                <i class="fa-solid fa-key"></i>
            </button>

            <div class="forgot-row">
                <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
            </div>
        <p class="text-muted" style="font-size:12px;text-align:center;margin-top:10px">Password: 5-8 characters, letters + numbers + symbols</p></form>
    </div>
</div>

<script>
function toggleFieldPassword(fieldId, iconId) {
  const pass = document.getElementById(fieldId);
  const icon = document.getElementById(iconId);
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

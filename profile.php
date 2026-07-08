<?php
include "auth.php";
$pageTitle = "My Profile";
$msg = "";
$err = "";

$uid = (int)($_SESSION['user_id'] ?? 0);

function get_profile_user($conn, $uid) {
    $user = null;

    if ($uid > 0) {
        $res = mysqli_query($conn, "SELECT * FROM users WHERE id=$uid LIMIT 1");
        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
        }
    }

    // Fix for old session after database re-import:
    // If session user_id does not exist anymore, find the correct user again.
    if (!$user) {
        $sessionName = mysqli_real_escape_string($conn, $_SESSION['name'] ?? '');
        $sessionRole = mysqli_real_escape_string($conn, $_SESSION['role'] ?? '');

        if ($sessionRole === 'admin') {
            $res = mysqli_query($conn, "SELECT * FROM users WHERE username='admin' LIMIT 1");
        } else {
            $res = mysqli_query($conn, "SELECT * FROM users WHERE full_name='$sessionName' LIMIT 1");
        }

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
            $_SESSION['user_id'] = $user['id'];
        }
    }

    return $user;
}

$user = get_profile_user($conn, $uid);

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$uid = (int)$user['id'];

if (isset($_POST['update_profile'])) {
    $name = clean($_POST['full_name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $picPath = upload_profile_picture('profile_picture');

    $picSql = "";
    if ($picPath) {
        $picSql = ", profile_picture='$picPath'";
    }

    $ok = mysqli_query($conn, "UPDATE users SET full_name='$name', email='$email' $picSql WHERE id=$uid");

    if ($ok) {
        $_SESSION['name'] = $name;
        $msg = "Profile updated successfully.";
        $user = get_profile_user($conn, $uid);
    } else {
        $err = "Profile update failed. Please import update_existing_database.sql if you updated an old database.";
    }
}

if (isset($_POST['change_password'])) {
    $newPass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($newPass !== $confirm) {
        $err = "Password confirmation does not match.";
    } elseif (!valid_password_rule($newPass)) {
        $err = password_rule_message();
    } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $ok = mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$uid");

        if ($ok) {
            $msg = "Password changed successfully.";
        } else {
            $err = "Password change failed.";
        }
    }
}

$fullName = $user['full_name'] ?? ($_SESSION['name'] ?? 'User');
$email = $user['email'] ?? '';
$role = $user['role'] ?? ($_SESSION['role'] ?? 'user');
$profilePicture = $user['profile_picture'] ?? '';
$initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $fullName) ?: 'US', 0, 2));
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<?php if($err): ?><div class="alert-banner alert-danger"><?php echo h($err); ?></div><?php endif; ?>

<div class="profile-layout profile-stack-layout" style="display:flex;flex-direction:column;gap:18px;max-width:720px;margin:0 auto;align-items:stretch;">
  <div class="card profile-card" style="width:100%;max-width:720px;flex:none;">
    <div class="card-body" style="text-align:center">
      <?php if(!empty($profilePicture)): ?>
        <img class="profile-preview" src="<?php echo h($profilePicture); ?>" alt="Profile Picture">
      <?php else: ?>
        <div class="profile-preview placeholder"><?php echo h($initials); ?></div>
      <?php endif; ?>
      <h2><?php echo h($fullName); ?></h2>
      <p class="text-muted"><?php echo h($email); ?></p>
      <span class="badge badge-i"><?php echo strtoupper(h($role)); ?></span>
    </div>
  </div>

  <div class="card" style="width:100%;max-width:720px;flex:none;">
    <div class="card-hdr"><h3>Update Profile</h3></div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="fg"><label>Full Name</label><input name="full_name" value="<?php echo h($fullName); ?>" required></div>
          <div class="fg"><label>Email</label><input type="email" name="email" value="<?php echo h($email); ?>"></div>
          <div class="fg"><label>Profile Picture</label><input type="file" name="profile_picture" accept="image/*"></div>
        </div>
        <button class="btn-sm btn-primary" name="update_profile"><i class="fa-solid fa-save"></i> Update Profile</button>
      </form>
    </div>
  </div>

  <div class="card" style="width:100%;max-width:720px;flex:none;">
    <div class="card-hdr"><h3>Change Password</h3></div>
    <div class="card-body">
      <form method="POST">
        <div class="form-grid">
          <div class="fg"><label>New Password</label><input data-secure-password="1" type="password" name="new_password" minlength="5" maxlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,8}" title="5-8 characters with letters, numbers and symbols" required><small class="text-muted">5-8 characters, letters + numbers + symbols</small></div>
          <div class="fg"><label>Confirm Password</label><input data-secure-password="1" type="password" name="confirm_password" minlength="5" maxlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,8}" title="5-8 characters with letters, numbers and symbols" required></div>
        </div>
        <button class="btn-sm btn-warning" name="change_password"><i class="fa-solid fa-key"></i> Change Password</button>
      </form>
    </div>
  </div>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>

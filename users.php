<?php
include "auth.php";
require_admin();
$pageTitle = "Users";
$msg="";
$msgType="success";
if(isset($_POST['add'])){
    $name=clean($_POST['full_name']);
    $u=clean($_POST['username']);
    $e=clean($_POST['email']);
    $r=clean($_POST['role']);
    $plainPassword = $_POST['password'] ?? '';

    $checkUsername = mysqli_query($conn, "SELECT id FROM users WHERE username='$u' LIMIT 1");
    $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email='$e' LIMIT 1");

    if ($checkUsername && mysqli_num_rows($checkUsername) > 0) {
        $msg = "This username already exsists";
        $msgType = "danger";
    } elseif ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
        $msg = "This email already exsists";
        $msgType = "danger";
    } elseif (!valid_password_rule($plainPassword)) {
        $msg = password_rule_message();
        $msgType = "danger";
    } else {
        $pass = password_hash($plainPassword, PASSWORD_DEFAULT);
        $profilePic = upload_profile_picture('profile_picture');
        $profileSqlValue = $profilePic ? "'$profilePic'" : "NULL";

        $insertUser = mysqli_query($conn, "INSERT INTO users(full_name,username,email,password,role,status,profile_picture) VALUES('$name','$u','$e','$pass','$r','active',$profileSqlValue)");

        if ($insertUser) {
            $subject = "Welcome to SDR IMS";
            $message = "Welcome to SDR IMS\n\nHello $name,\n\nYour SDR IMS account has been created successfully.\n\nUsername: $u\nPassword: $plainPassword\nRole: $r\n\nPlease login and change your password after first use.\n\nDeveloped by Samiru Dinushan";
            $headers = "From: no-reply@sdrims.local\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail($e, $subject, $message, $headers);
            $msg="User added. Welcome email sent if your server mail is configured.";
            $msgType = "success";
        } else {
            $msg="User add failed: " . mysqli_error($conn);
            $msgType = "danger";
        }
    }
}
if(isset($_GET['toggle'])){
    $id=(int)$_GET['toggle'];
    mysqli_query($conn,"UPDATE users SET status=IF(status='active','inactive','active') WHERE id=$id AND id!=".(int)$_SESSION['user_id']);
    header("Location: users.php");
    exit();
}
$rows=mysqli_query($conn,"SELECT * FROM users ORDER BY id ASC");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-<?php echo h($msgType); ?>"><?php echo h($msg); ?></div><?php endif; ?>
<div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
  <div class="card" style="width:320px;flex-shrink:0">
    <div class="card-hdr"><h3>Add User</h3></div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="fg"><label>Full Name *</label><input name="full_name" placeholder="Enter Full Name" required></div>
        <div class="fg"><label>Username *</label><input name="username" placeholder="Enter Username" required></div>
        <div class="fg"><label>Email *</label><input type="email" name="email" placeholder="Enter Email" required ></div>
        <div class="fg">
          <label>Password *</label>
          <div class="password-box add-user-password-box">
            <button type="button" class="password-eye-btn-left" onclick="toggleAddUserPassword()" aria-label="Show password">
              <span id="addUserPasswordIcon">👁</span>
            </button>
            <input data-secure-password="1" type="password" name="password" id="addUserPassword" placeholder="Example: Abc@1" minlength="5" maxlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{5,8}" title="5-8 characters with letters, numbers and symbols" required>
          </div>
          <small class="text-muted">5-8 characters, letters + numbers + symbols</small>
        </div>
        <div class="fg"><label>Role</label><select name="role"><option value="staff">Staff</option><option value="manager">Manager</option><option value="admin">Admin</option></select></div>
        <div class="fg"><label>Profile Picture</label><input type="file" name="profile_picture" accept="image/*"></div>
        <button class="btn-sm btn-primary" name="add" style="width:100%;padding:9px"><i class="fa-solid fa-plus"></i> Add User</button>
      </form>
    </div>
  </div>
  <div class="card" style="flex:1;min-width:600px">
    <div class="card-hdr"><h3>All Users</h3></div>
    <table><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th><th>Action</th></tr></thead><tbody>
    <?php while($r=mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><div style="display:flex;align-items:center;gap:8px"><?php if(!empty($r['profile_picture'])): ?><img class="profile-avatar-img small" src="<?php echo h($r['profile_picture']); ?>" alt="Profile"><?php else: ?><div class="u-avatar" style="width:32px;height:32px;font-size:11px"><?php echo strtoupper(substr($r['full_name'],0,2)); ?></div><?php endif; ?><div><b><?php echo h($r['full_name']); ?></b><br><small class="text-muted">@<?php echo h($r['username']); ?></small></div></div></td>
        <td><?php echo h($r['email']); ?></td>
        <td><span class="badge <?php echo $r['role']=='admin'?'badge-d':($r['role']=='manager'?'badge-w':'badge-i'); ?>"><?php echo strtoupper($r['role']); ?></span></td>
        <td><?php echo $r['last_login'] ? h($r['last_login']) : '—'; ?></td>
        <td><span class="badge <?php echo $r['status']=='active'?'badge-s':'badge-d'; ?>"><?php echo h(ucfirst($r['status'])); ?></span></td>
        <td><?php if($r['id']==$_SESSION['user_id']): ?><button class="btn-sm" disabled>Current</button><?php else: ?><a class="btn-sm" href="?toggle=<?php echo $r['id']; ?>">Toggle</a><?php endif; ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody></table>
  </div>
</div>
</div>
</div>
<script>
function toggleAddUserPassword() {
  const pass = document.getElementById('addUserPassword');
  const icon = document.getElementById('addUserPasswordIcon');
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
<?php if($msg && in_array($msg, ['This username already exsists','This email already exsists'])): ?>
<script id="onlineUserDuplicatePopup">
window.addEventListener('load', function(){
  alert(<?php echo json_encode($msg); ?>);
});
</script>
<?php endif; ?>
<?php include "partials/footer.php"; ?>

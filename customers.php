<?php
include "auth.php";
$pageTitle = "Customers";
$msg="";
if(isset($_POST['add'])){
    $n=clean($_POST['customer_name']); $p=clean($_POST['phone']); $e=clean($_POST['email']); $a=clean($_POST['address']);
    if (!valid_phone($p)) {
        $msg = "Phone number must contain numbers only and maximum 10 characters.";
    } else {
        mysqli_query($conn,"INSERT INTO customers(customer_name,phone,email,address) VALUES('$n','$p','$e','$a')");
        $msg="Customer added.";
    }
}
if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; mysqli_query($conn,"DELETE FROM customers WHERE id=$id"); header("Location: customers.php"); exit(); }
$rows=mysqli_query($conn,"SELECT * FROM customers ORDER BY id DESC");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<div class="card">
  <div class="card-hdr"><h3>Add Customer</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid">
        <div class="fg"><label>Customer Name *</label><input name="customer_name" required></div>
        <div class="fg"><label>Phone</label><input name="phone" maxlength="10" pattern="[0-9]{0,10}" inputmode="numeric" placeholder="0719957871"></div>
        <div class="fg"><label>Email</label><input type="email" name="email"></div>
        <div class="fg"><label>Address</label><textarea name="address" rows="2"></textarea></div>
      </div>
      <button class="btn-sm btn-primary" name="add"><i class="fa-solid fa-plus"></i> Add Customer</button>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-hdr"><h3>All Customers</h3></div>
  <table><thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th>Action</th></tr></thead><tbody>
  <?php while($r=mysqli_fetch_assoc($rows)): ?>
    <tr><td><b><?php echo h($r['customer_name']); ?></b></td><td><?php echo h($r['phone']); ?></td><td><?php echo h($r['email']); ?></td><td><?php echo h($r['address']); ?></td><td><a class="btn-sm btn-danger" href="?delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete customer?')"><i class="fa-solid fa-trash"></i></a></td></tr>
  <?php endwhile; ?>
  </tbody></table>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>

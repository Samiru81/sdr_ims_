<?php
include "auth.php";
$pageTitle = "Suppliers";
$msg="";
if(isset($_POST['add'])){
    $n=clean($_POST['supplier_name']); $cp=clean($_POST['contact_person']); $p=clean($_POST['phone']); $e=clean($_POST['email']); $a=clean($_POST['address']);
    if (!valid_phone($p)) {
        $msg = "Phone number must contain numbers only and maximum 10 characters.";
    } else {
        mysqli_query($conn,"INSERT INTO suppliers(supplier_name,contact_person,phone,email,address) VALUES('$n','$cp','$p','$e','$a')");
        $msg="Supplier added.";
    }
}
if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; mysqli_query($conn,"DELETE FROM suppliers WHERE id=$id"); header("Location: suppliers.php"); exit(); }
$rows=mysqli_query($conn,"SELECT * FROM suppliers ORDER BY id DESC");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<div class="card">
  <div class="card-hdr"><h3>Add Supplier</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid">
        <div class="fg"><label>Supplier Name *</label><input name="supplier_name" required></div>
        <div class="fg"><label>Contact Person</label><input name="contact_person"></div>
        <div class="fg"><label>Phone</label><input name="phone" maxlength="10" pattern="[0-9]{0,10}" inputmode="numeric" placeholder="0719957871"></div>
        <div class="fg"><label>Email</label><input type="email" name="email"></div>
        <div class="fg"><label>Address</label><textarea name="address" rows="2"></textarea></div>
      </div>
      <button class="btn-sm btn-primary" name="add"><i class="fa-solid fa-plus"></i> Add Supplier</button>
    </form>
  </div>
</div>
<div class="sup-grid">
<?php while($r=mysqli_fetch_assoc($rows)): 
$letters = strtoupper(substr($r['supplier_name'],0,1) . substr(strstr($r['supplier_name'].' ', ' '),1,1));
?>
  <div class="sup-card">
    <div class="sup-av"><?php echo h($letters); ?></div>
    <div class="sup-body">
      <h4><?php echo h($r['supplier_name']); ?></h4>
      <div class="sup-meta">
        <div><i class="fa-solid fa-user"></i> <?php echo h($r['contact_person']); ?></div>
        <div><i class="fa-solid fa-phone"></i> <?php echo h($r['phone']); ?></div>
        <div><i class="fa-solid fa-envelope"></i> <?php echo h($r['email']); ?></div>
        <div><i class="fa-solid fa-location-dot"></i> <?php echo h($r['address']); ?></div>
        <div><a class="btn-sm btn-danger" href="?delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete supplier?')"><i class="fa-solid fa-trash"></i> Delete</a></div>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>

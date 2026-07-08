<?php
include "auth.php";
$pageTitle = "Purchase / Stock In";
$msg=""; $err="";
if(isset($_POST['purchase'])){
    $pid=(int)$_POST['product_id']; $sid=(int)$_POST['supplier_id']; $qty=(int)$_POST['quantity']; $price=(float)$_POST['cost_price']; $ref=clean($_POST['reference_no']);
    $p=mysqli_fetch_assoc(mysqli_query($conn,"SELECT quantity FROM products WHERE id=$pid"));
    if($p && $qty>0){
      $before=(int)$p['quantity']; $after=$before+$qty; $total=$qty*$price;
      mysqli_query($conn,"INSERT INTO purchases(product_id,supplier_id,quantity,cost_price,total,reference_no) VALUES($pid,$sid,$qty,$price,$total,'$ref')");
      mysqli_query($conn,"UPDATE products SET quantity=$after, cost_price=$price WHERE id=$pid");
      mysqli_query($conn,"INSERT INTO stock_movements(product_id,movement_type,quantity,before_qty,after_qty,reference_no,note,created_by) VALUES($pid,'IN',$qty,$before,$after,'$ref','Purchase / Stock In','".clean($_SESSION['name'])."')");
      $msg="Purchase saved and stock updated.";
    } else { $err="Invalid product or quantity."; }
}
$products=mysqli_query($conn,"SELECT * FROM products ORDER BY product_name");
$suppliers=mysqli_query($conn,"SELECT * FROM suppliers ORDER BY supplier_name");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<?php if($err): ?><div class="alert-banner alert-danger"><?php echo h($err); ?></div><?php endif; ?>
<div class="card">
  <div class="card-hdr"><h3>Stock In Entry</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid">
        <div class="fg"><label>Product *</label><select name="product_id" required><?php while($p=mysqli_fetch_assoc($products)): ?><option value="<?php echo $p['id']; ?>"><?php echo h($p['product_name']); ?> - <?php echo h($p['sku']); ?></option><?php endwhile; ?></select></div>
        <div class="fg"><label>Supplier</label><select name="supplier_id"><?php while($s=mysqli_fetch_assoc($suppliers)): ?><option value="<?php echo $s['id']; ?>"><?php echo h($s['supplier_name']); ?></option><?php endwhile; ?></select></div>
        <div class="fg"><label>Quantity *</label><input type="number" name="quantity" required></div>
        <div class="fg"><label>Cost Price *</label><input type="number" step="0.01" name="cost_price" required></div>
        <div class="fg"><label>Reference No</label><input name="reference_no" value="PO-<?php echo date('Ymd-His'); ?>"></div>
      </div>
      <button class="btn-sm btn-success" name="purchase"><i class="fa-solid fa-plus"></i> Save Stock In</button>
    </form>
  </div>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>

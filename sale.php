<?php
include "auth.php";
$pageTitle = "Sale / Stock Out";
$msg=""; $err="";
if(isset($_POST['sale'])){
    $pid=(int)$_POST['product_id'];
    $cid=(int)$_POST['customer_id'];
    $qty=(int)$_POST['quantity'];
    $price=(float)$_POST['selling_price'];
    $discount=max(0, (float)$_POST['discount']);
    $ref=clean($_POST['reference_no']);
    $issuedBy=clean($_SESSION['name'] ?? $_SESSION['role'] ?? 'System User');
    $p=mysqli_fetch_assoc(mysqli_query($conn,"SELECT quantity FROM products WHERE id=$pid"));
    if($p && $qty>0 && $p['quantity'] >= $qty){
      $before=(int)$p['quantity'];
      $after=$before-$qty;
      $subtotal=$qty*$price;
      $total=max(0, $subtotal-$discount);
      $saleInsert = mysqli_query($conn,"INSERT INTO sales(product_id,customer_id,quantity,selling_price,subtotal,discount,total,reference_no,issued_by) VALUES($pid,$cid,$qty,$price,$subtotal,$discount,$total,'$ref','$issuedBy')");
      if ($saleInsert) {
        $sale_id = mysqli_insert_id($conn);
        mysqli_query($conn,"UPDATE products SET quantity=$after, selling_price=$price WHERE id=$pid");
        mysqli_query($conn,"INSERT INTO stock_movements(product_id,movement_type,quantity,before_qty,after_qty,reference_no,note,created_by) VALUES($pid,'OUT',$qty,$before,$after,'$ref','Sale / Stock Out','".clean($_SESSION['name'])."')");
        header("Location: bill.php?id=" . $sale_id);
        exit();
      } else {
        $err="Sale save failed: " . mysqli_error($conn);
      }
    } else { $err="Not enough stock available or invalid quantity."; }
}
$products=mysqli_query($conn,"SELECT * FROM products ORDER BY product_name");
$customers=mysqli_query($conn,"SELECT * FROM customers ORDER BY customer_name");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<?php if($err): ?><div class="alert-banner alert-danger"><?php echo h($err); ?></div><?php endif; ?>
<div class="card">
  <div class="card-hdr"><h3>Stock Out / Sale Entry</h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid">
        <div class="fg"><label>Barcode Reader</label><input type="text" id="saleBarcode" placeholder="Scan barcode here" autocomplete="off"><small class="text-muted">Use USB/Bluetooth/mobile barcode scanner. Scanner works like keyboard input.</small></div>
        <div class="fg"><label>Product *</label><select name="product_id" id="productSelect" required><?php while($p=mysqli_fetch_assoc($products)): ?><option value="<?php echo $p['id']; ?>" data-barcode="<?php echo h($p['barcode'] ?? ''); ?>" data-sku="<?php echo h($p['sku']); ?>" data-price="<?php echo h($p['selling_price']); ?>"><?php echo h($p['product_name']); ?> - Stock: <?php echo $p['quantity']; ?> - <?php echo h($p['sku']); ?></option><?php endwhile; ?></select></div>
        <div class="fg"><label>Customer</label><select name="customer_id"><?php while($c=mysqli_fetch_assoc($customers)): ?><option value="<?php echo $c['id']; ?>"><?php echo h($c['customer_name']); ?></option><?php endwhile; ?></select></div>
        <div class="fg"><label>Quantity *</label><input type="number" name="quantity" required></div>
        <div class="fg"><label>Selling Price *</label><input type="number" step="0.01" name="selling_price" id="sellingPrice" required></div>
        <div class="fg"><label>Discount</label><input type="number" step="0.01" name="discount" value="0" placeholder="Discount amount"></div>
        <div class="fg"><label>Reference No</label><input name="reference_no" value="SO-<?php echo date('Ymd-His'); ?>"></div>
      </div>
      <button class="btn-sm btn-success" name="sale"><i class="fa-solid fa-cart-shopping"></i> Save Sale</button>
    </form>
  </div>
</div>
</div>
</div>
<script>
// Barcode reader script: USB/Bluetooth scanners type the barcode and press Enter.
const barcodeInput = document.getElementById('saleBarcode');
const productSelect = document.getElementById('productSelect');
const sellingPrice = document.getElementById('sellingPrice');

function fillPrice() {
  const opt = productSelect.options[productSelect.selectedIndex];
  if (opt && opt.dataset.price) sellingPrice.value = opt.dataset.price;
}
productSelect.addEventListener('change', fillPrice);
fillPrice();

barcodeInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    const code = barcodeInput.value.trim();
    let found = false;
    [...productSelect.options].forEach((opt, idx) => {
      if (opt.dataset.barcode === code || opt.dataset.sku === code) {
        productSelect.selectedIndex = idx;
        found = true;
        fillPrice();
      }
    });
    if (!found && code.length > 0) {
      alert('Barcode not found: ' + code);
    }
    barcodeInput.value = '';
  }
});
</script>
<?php include "partials/footer.php"; ?>

<?php
include "auth.php";
$pageTitle = "Products";

$msg = "";
$search = clean($_GET['search'] ?? "");

if (isset($_POST['save_product'])) {
    $id = (int)($_POST['id'] ?? 0);
    $sku = clean($_POST['sku']);
    $barcode = clean($_POST['barcode']);
    $product_name = clean($_POST['product_name']);
    $category_id = (int)$_POST['category_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $unit = clean($_POST['unit']);
    $location = clean($_POST['location']);
    $quantity = (int)$_POST['quantity'];
    $min_stock = max(10, (int)$_POST['min_stock']);
    $cost_price = (float)$_POST['cost_price'];
    $selling_price = (float)$_POST['selling_price'];

    if ($id > 0) {
        mysqli_query($conn, "UPDATE products SET sku='$sku', barcode='$barcode', product_name='$product_name', category_id=$category_id, supplier_id=$supplier_id, unit='$unit', location='$location', quantity=$quantity, min_stock=$min_stock, cost_price=$cost_price, selling_price=$selling_price WHERE id=$id");
        $msg = "Product updated successfully.";
    } else {
        mysqli_query($conn, "INSERT INTO products(sku,barcode,product_name,category_id,supplier_id,unit,location,quantity,min_stock,cost_price,selling_price) VALUES('$sku','$barcode','$product_name',$category_id,$supplier_id,'$unit','$location',$quantity,$min_stock,$cost_price,$selling_price)");
        $msg = "Product added successfully.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$editId"));
}

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
$sups = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY supplier_name");

$where = $search ? "WHERE p.product_name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.barcode LIKE '%$search%' OR c.category_name LIKE '%$search%'" : "";
$rows = mysqli_query($conn, "SELECT p.*, c.category_name, c.color, s.supplier_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN suppliers s ON p.supplier_id=s.id $where ORDER BY p.id DESC");
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products"))['total'];
$low = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products WHERE quantity <= min_stock"))['total'];
$out = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products WHERE quantity = 0"))['total'];
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
    <?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>

    <div class="page-head">
      <div class="tab-bar">
        <a class="tab active" href="products.php">All (<?php echo $total; ?>)</a>
        <a class="tab" href="low_stock.php">Low Stock (<?php echo $low; ?>)</a>
        <span class="tab">Out of Stock (<?php echo $out; ?>)</span>
      </div>
      <a class="btn-sm btn-primary" href="#product-form"><i class="fa-solid fa-plus"></i> Add Product</a>
    </div>

    <div class="card" id="product-form">
      <div class="card-hdr"><h3><?php echo $edit ? 'Edit Product' : 'Add Product'; ?></h3></div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo h($edit['id'] ?? ''); ?>">
          <div class="form-grid">
            <div class="fg"><label>SKU *</label><input name="sku" value="<?php echo h($edit['sku'] ?? ''); ?>" required></div>
            <div class="fg"><label>Barcode</label><input name="barcode" value="<?php echo h($edit['barcode'] ?? ''); ?>" placeholder="Scan or type barcode"></div>
            <div class="fg"><label>Product Name *</label><input name="product_name" value="<?php echo h($edit['product_name'] ?? ''); ?>" required></div>
            <div class="fg"><label>Category</label><select name="category_id"><?php while($c=mysqli_fetch_assoc($cats)): ?><option value="<?php echo $c['id']; ?>" <?php echo (($edit['category_id'] ?? '')==$c['id'])?'selected':''; ?>><?php echo h($c['category_name']); ?></option><?php endwhile; ?></select></div>
            <div class="fg"><label>Supplier</label><select name="supplier_id"><?php while($s=mysqli_fetch_assoc($sups)): ?><option value="<?php echo $s['id']; ?>" <?php echo (($edit['supplier_id'] ?? '')==$s['id'])?'selected':''; ?>><?php echo h($s['supplier_name']); ?></option><?php endwhile; ?></select></div>
            <div class="fg"><label>Unit</label><input name="unit" value="<?php echo h($edit['unit'] ?? 'pcs'); ?>"></div>
            <div class="fg"><label>Location</label><input name="location" value="<?php echo h($edit['location'] ?? ''); ?>"></div>
            <div class="fg"><label>Quantity</label><input type="number" name="quantity" value="<?php echo h($edit['quantity'] ?? 0); ?>"></div>
            <div class="fg"><label>Minimum Stock (Alert starts at 10)</label><input type="number" min="10" name="min_stock" value="<?php echo h($edit['min_stock'] ?? 10); ?>"></div>
            <div class="fg"><label>Cost Price</label><input type="number" step="0.01" name="cost_price" value="<?php echo h($edit['cost_price'] ?? 0); ?>"></div>
            <div class="fg"><label>Selling Price</label><input type="number" step="0.01" name="selling_price" value="<?php echo h($edit['selling_price'] ?? 0); ?>"></div>
          </div>
          <button class="btn-sm btn-primary" name="save_product" type="submit"><i class="fa-solid fa-save"></i> Save Product</button>
          <?php if($edit): ?><a class="btn-sm" href="products.php">Cancel</a><?php endif; ?>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-hdr"><h3>Products</h3></div>
      <table>
        <thead><tr><th>SKU / Barcode</th><th>Product</th><th>Category</th><th>Cost</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($r=mysqli_fetch_assoc($rows)): 
          $percent = $r['min_stock'] > 0 ? min(100, round(($r['quantity'] / max($r['min_stock']*2, 1))*100)) : 100;
          $isLow = $r['quantity'] <= $r['min_stock'];
        ?>
          <tr>
            <td><span class="sku"><?php echo h($r['sku']); ?></span><br><small class="text-muted"><i class="fa-solid fa-barcode"></i> <?php echo h($r['barcode']); ?></small></td>
            <td><b><?php echo h($r['product_name']); ?></b><br><small class="text-muted"><i class="fa-solid fa-location-dot"></i> <?php echo h($r['location']); ?></small></td>
            <td><span class="cat-dot" style="background:<?php echo h($r['color'] ?? '#4f8ef7'); ?>"></span><?php echo h($r['category_name']); ?></td>
            <td><?php echo money($r['cost_price']); ?></td>
            <td><?php echo money($r['selling_price']); ?></td>
            <td><div><b style="<?php echo $isLow?'color:var(--danger)':''; ?>"><?php echo $r['quantity']; ?></b> <span class="text-muted" style="font-size:11px"><?php echo h($r['unit']); ?></span></div><div class="sbar"><div class="sbar-fill" style="width:<?php echo $percent; ?>%;background:<?php echo $isLow?'var(--danger)':'var(--success)'; ?>"></div></div></td>
            <td><span class="badge <?php echo $isLow?'badge-w':'badge-s'; ?>"><?php echo $isLow?'Low Stock':'In Stock'; ?></span></td>
            <td><div class="actions"><a class="btn-sm" href="products.php?edit=<?php echo $r['id']; ?>#product-form"><i class="fa-solid fa-pen"></i></a><a class="btn-sm btn-danger" href="products.php?delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete this product?')"><i class="fa-solid fa-trash"></i></a></div></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
</div>
</div>
<?php include "partials/footer.php"; ?>

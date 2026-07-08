<?php
include "auth.php";
$pageTitle = "Low Stock";
$rows = mysqli_query($conn, "SELECT p.*, c.category_name, c.color FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.quantity <= p.min_stock ORDER BY p.quantity ASC");
$count = mysqli_num_rows($rows);
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
  <?php if($count > 0): ?><div class="alert-banner alert-warn"><i class="fa-solid fa-triangle-exclamation"></i> <b><?php echo $count; ?> products</b> below minimum stock level.</div><?php endif; ?>
  <div class="card">
    <div class="card-hdr"><h3>Low Stock Items</h3><a class="btn-sm" href="products.php">Back to Products</a></div>
    <table>
      <thead><tr><th>SKU</th><th>Product</th><th>Category</th><th>Quantity</th><th>Minimum</th><th>Status</th></tr></thead>
      <tbody>
      <?php if($count==0): ?><tr><td colspan="6" class="empty">No low stock items.</td></tr><?php endif; ?>
      <?php while($r=mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><span class="sku"><?php echo h($r['sku']); ?></span></td>
          <td><b><?php echo h($r['product_name']); ?></b></td>
          <td><span class="cat-dot" style="background:<?php echo h($r['color'] ?? '#4f8ef7'); ?>"></span><?php echo h($r['category_name']); ?></td>
          <td style="color:var(--danger)"><b><?php echo $r['quantity']; ?></b> <?php echo h($r['unit']); ?></td>
          <td><?php echo $r['min_stock']; ?></td>
          <td><span class="badge badge-w">Low Stock</span></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include "partials/footer.php"; ?>

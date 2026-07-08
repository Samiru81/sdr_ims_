<?php
include "auth.php";
$pageTitle = "Stock Movements";
$type = clean($_GET['type'] ?? '');
$where = $type ? "WHERE m.movement_type='$type'" : "";
$rows = mysqli_query($conn, "SELECT m.*, p.product_name, p.sku FROM stock_movements m JOIN products p ON m.product_id=p.id $where ORDER BY m.id DESC");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
  <div class="page-head">
    <div class="tab-bar">
      <a class="tab <?php echo $type==''?'active':''; ?>" href="stock.php">All</a>
      <a class="tab <?php echo $type=='IN'?'active':''; ?>" href="stock.php?type=IN">In</a>
      <a class="tab <?php echo $type=='OUT'?'active':''; ?>" href="stock.php?type=OUT">Out</a>
      <a class="tab <?php echo $type=='ADJUSTMENT'?'active':''; ?>" href="stock.php?type=ADJUSTMENT">Adjustment</a>
    </div>
    <a class="btn-sm btn-primary" href="purchase.php"><i class="fa-solid fa-plus"></i> Add Movement</a>
  </div>
  <div class="card">
    <div class="card-hdr"><h3>Stock Movements</h3></div>
    <table>
      <thead><tr><th>#</th><th>Product</th><th>Type</th><th>Qty</th><th>Before</th><th>After</th><th>Reference</th><th>By</th><th>Date</th></tr></thead>
      <tbody>
      <?php while($r=mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td class="text-muted"><?php echo $r['id']; ?></td>
          <td><b><?php echo h($r['product_name']); ?></b><br><small class="text-muted"><?php echo h($r['sku']); ?></small></td>
          <td><span class="badge <?php echo $r['movement_type']=='IN'?'badge-s':($r['movement_type']=='OUT'?'badge-d':'badge-i'); ?>"><?php echo h($r['movement_type']); ?></span></td>
          <td style="color:<?php echo $r['movement_type']=='OUT'?'var(--danger)':'var(--success)'; ?>"><b><?php echo $r['movement_type']=='OUT'?'-':'+'; ?><?php echo $r['quantity']; ?></b></td>
          <td><?php echo $r['before_qty']; ?></td>
          <td><?php echo $r['after_qty']; ?></td>
          <td><small><?php echo h($r['reference_no']); ?></small></td>
          <td><?php echo h($r['created_by']); ?></td>
          <td><?php echo date('d M Y H:i', strtotime($r['movement_date'])); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include "partials/footer.php"; ?>

<?php
include "auth.php";
$pageTitle = "Dashboard";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products"))['total'] ?? 0;
$inventory_value = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * cost_price) total FROM products"))['total'] ?? 0;
$retail_value = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * selling_price) total FROM products"))['total'] ?? 0;
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products WHERE quantity <= min_stock"))['total'] ?? 0;
$out_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM products WHERE quantity = 0"))['total'] ?? 0;
$suppliers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM suppliers"))['total'] ?? 0;
$movements_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM stock_movements WHERE DATE(movement_date)=CURDATE()"))['total'] ?? 0;

$lowRows = mysqli_query($conn, "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.quantity <= p.min_stock ORDER BY p.quantity ASC LIMIT 5");
$recentRows = mysqli_query($conn, "SELECT m.*, p.product_name, p.sku FROM stock_movements m JOIN products p ON m.product_id=p.id ORDER BY m.id DESC LIMIT 5");
$catRows = mysqli_query($conn, "SELECT c.category_name, c.color, COUNT(p.id) total FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY total DESC");
$catLabels=[]; $catData=[]; $catColors=[];
while($c=mysqli_fetch_assoc($catRows)){ $catLabels[]=$c['category_name']; $catData[]=(int)$c['total']; $catColors[]=$c['color']; }
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">

    <?php if((int)$low_stock > 0): ?>
    <div class="dashboard-low-stock-banner">
      <div>
        <strong><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alert</strong>
        <p><?php echo (int)$low_stock; ?> product(s) are currently low stock. Please check and restock soon.</p>
      </div>
      <a class="btn-sm btn-primary" href="low_stock.php">View Low Stock</a>
    </div>
    <?php endif; ?>

    <div class="stats-grid">
      <div class="stat"><div class="stat-ico p"><i class="fa-solid fa-boxes-stacked"></i></div><div><div class="stat-val"><?php echo $total_products; ?></div><div class="stat-lbl">Total Products</div></div></div>
      <div class="stat"><div class="stat-ico b"><i class="fa-solid fa-money-bill-wave"></i></div><div><div class="stat-val" style="font-size:14px"><?php echo money($inventory_value); ?></div><div class="stat-lbl">Inventory Value</div></div></div>
      <div class="stat"><div class="stat-ico o"><i class="fa-solid fa-triangle-exclamation"></i></div><div><div class="stat-val"><?php echo $low_stock; ?></div><div class="stat-lbl">Low Stock</div></div></div>
      <div class="stat"><div class="stat-ico r"><i class="fa-solid fa-ban"></i></div><div><div class="stat-val"><?php echo $out_stock; ?></div><div class="stat-lbl">Out of Stock</div></div></div>
      <div class="stat"><div class="stat-ico g"><i class="fa-solid fa-truck"></i></div><div><div class="stat-val"><?php echo $suppliers; ?></div><div class="stat-lbl">Suppliers</div></div></div>
      <div class="stat"><div class="stat-ico t"><i class="fa-solid fa-arrow-right-arrow-left"></i></div><div><div class="stat-val"><?php echo $movements_today; ?></div><div class="stat-lbl">Movements Today</div></div></div>
    </div>

    <div class="dash-grid">
      <div class="card"><div class="card-hdr"><h3>Stock Movement</h3></div><div class="card-body"><canvas id="barChart" height="200"></canvas></div></div>
      <div class="card"><div class="card-hdr"><h3>By Category</h3></div><div class="card-body"><canvas id="donutChart" height="200"></canvas></div></div>
    </div>

    <div class="dash-grid">
      <div class="card">
        <div class="card-hdr"><h3><i class="fa-solid fa-triangle-exclamation" style="color:var(--warning)"></i>&nbsp; Low Stock Alert</h3><a class="btn-sm" href="low_stock.php">View All</a></div>
        <table>
          <thead><tr><th>Product</th><th>Cat</th><th>Qty</th><th>Min</th><th>Status</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($lowRows)==0): ?>
            <tr><td colspan="5" class="empty">No low stock products.</td></tr>
          <?php endif; ?>
          <?php while($r=mysqli_fetch_assoc($lowRows)): ?>
            <tr>
              <td><b><?php echo h($r['product_name']); ?></b><br><small class="text-muted"><?php echo h($r['sku']); ?></small></td>
              <td><?php echo h($r['category_name']); ?></td>
              <td><b style="color:var(--danger)"><?php echo $r['quantity']; ?></b></td>
              <td><?php echo $r['min_stock']; ?></td>
              <td><span class="badge badge-w">Low Stock</span></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div class="card">
        <div class="card-hdr"><h3>Recent Movements</h3><a class="btn-sm" href="stock.php">View All</a></div>
        <table>
          <thead><tr><th>Product</th><th>Type</th><th>Qty</th><th>Date</th></tr></thead>
          <tbody>
          <?php while($m=mysqli_fetch_assoc($recentRows)): ?>
            <tr>
              <td><b><?php echo h($m['product_name']); ?></b></td>
              <td><span class="badge <?php echo $m['movement_type']=='IN'?'badge-s':($m['movement_type']=='OUT'?'badge-d':'badge-i'); ?>"><?php echo h($m['movement_type']); ?></span></td>
              <td style="color:<?php echo $m['movement_type']=='OUT'?'var(--danger)':'var(--success)'; ?>"><?php echo $m['movement_type']=='OUT'?'-':'+'; ?><?php echo $m['quantity']; ?></td>
              <td><?php echo date('d M', strtotime($m['movement_date'])); ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
</div>
</div>
<script>
const categoryLabels = <?php echo json_encode($catLabels); ?>;
const categoryData = <?php echo json_encode($catData); ?>;
const categoryColors = <?php echo json_encode($catColors); ?>;
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Today'],
    datasets: [
      {label:'Stock In', data:[25,10,0,15,50,8,0], backgroundColor:'rgba(16,185,129,0.8)', borderRadius:5},
      {label:'Stock Out',data:[0,47,18,0,4,0,0],  backgroundColor:'rgba(239,68,68,0.8)',  borderRadius:5}
    ]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:'#94a3b8'}}},scales:{x:{ticks:{color:'#94a3b8'},grid:{color:'rgba(255,255,255,0.04)'}},y:{ticks:{color:'#94a3b8'},grid:{color:'rgba(255,255,255,0.04)'}}}}
});
new Chart(document.getElementById('donutChart'), {
  type: 'doughnut',
  data: { labels: categoryLabels, datasets: [{data:categoryData, backgroundColor:categoryColors, borderWidth:0, hoverOffset:8}] },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{color:'#94a3b8',padding:10,font:{size:12}}}}}
});
</script>

<?php if((int)$low_stock > 0): ?>
<div class="low-stock-popup-overlay" id="lowStockPopup">
  <div class="low-stock-popup-card">
    <button class="low-stock-popup-close" onclick="closeLowStockPopup()">×</button>
    <div class="low-stock-popup-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <h2>Low Stock Alert</h2>
    <p><strong><?php echo (int)$low_stock; ?></strong> product(s) are currently low stock.</p>
    <div class="low-stock-popup-actions">
      <a class="btn-sm btn-primary" href="low_stock.php">View Low Stock</a>
      <button class="btn-sm" onclick="closeLowStockPopup()">Later</button>
    </div>
  </div>
</div>
<script>
function closeLowStockPopup(){
  var popup = document.getElementById('lowStockPopup');
  if(popup){ popup.classList.add('hide'); setTimeout(function(){ popup.style.display='none'; }, 220); }
}
window.addEventListener('load', function(){
  var popup = document.getElementById('lowStockPopup');
  if(popup){
    setTimeout(function(){ popup.classList.add('show'); }, 450);
  }
});
</script>
<?php endif; ?>

<?php include "partials/footer.php"; ?>

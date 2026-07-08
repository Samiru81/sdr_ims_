<?php
include "auth.php";
$pageTitle = "Reports";

function scalar_total($conn, $sql, $key = 'total') {
    $res = mysqli_query($conn, $sql);
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        return $row[$key] ?? 0;
    }
    return 0;
}

$total_skus   = (int)scalar_total($conn, "SELECT COUNT(*) total FROM products");
$cost_value   = (float)scalar_total($conn, "SELECT COALESCE(SUM(quantity * cost_price),0) total FROM products");
$retail_value = (float)scalar_total($conn, "SELECT COALESCE(SUM(quantity * selling_price),0) total FROM products");
$profit       = $retail_value - $cost_value;
$low          = (int)scalar_total($conn, "SELECT COUNT(*) total FROM products WHERE quantity <= min_stock");

$catRows = mysqli_query($conn, "
    SELECT 
        c.category_name,
        COALESCE(c.color, '#4f8ef7') AS color,
        COUNT(p.id) AS products,
        COALESCE(SUM(p.quantity), 0) AS qty,
        COALESCE(SUM(p.quantity * p.cost_price), 0) AS value
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.category_name, c.color
    ORDER BY value DESC, c.category_name ASC
");

$lowRows = mysqli_query($conn, "
    SELECT 
        p.product_name,
        p.sku,
        p.quantity,
        p.min_stock,
        COALESCE(c.category_name, 'Uncategorized') AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.quantity <= p.min_stock
    ORDER BY p.quantity ASC, p.product_name ASC
");

$movementMap = [];
$trendQuery = mysqli_query($conn, "
    SELECT 
        DATE(movement_date) AS d,
        SUM(CASE WHEN movement_type='IN' THEN quantity ELSE 0 END) AS qty_in,
        SUM(CASE WHEN movement_type='OUT' THEN quantity ELSE 0 END) AS qty_out
    FROM stock_movements
    WHERE movement_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
    GROUP BY DATE(movement_date)
    ORDER BY d ASC
");
if ($trendQuery) {
    while ($row = mysqli_fetch_assoc($trendQuery)) {
        $movementMap[$row['d']] = [
            'in' => (int)($row['qty_in'] ?? 0),
            'out' => (int)($row['qty_out'] ?? 0),
        ];
    }
}

$trendLabels = [];
$trendIn = [];
$trendOut = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $trendLabels[] = date('M j', strtotime($date));
    $trendIn[] = isset($movementMap[$date]) ? (int)$movementMap[$date]['in'] : 0;
    $trendOut[] = isset($movementMap[$date]) ? (int)$movementMap[$date]['out'] : 0;
}
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main reports-main">
<?php include "partials/topbar.php"; ?>
<div class="content reports-content">
  <?php if($low > 0): ?>
    <div class="alert-banner alert-warn report-alert-strip">
      <div><i class="fa-solid fa-triangle-exclamation"></i> <strong><?php echo (int)$low; ?> products</strong> below minimum stock level.</div>
      <a class="btn-sm btn-primary" href="low_stock.php">View Report</a>
    </div>
  <?php endif; ?>

  <div class="report-print-sheet" id="reportPrintSheet">
    <div class="report-sheet-header">
      <div class="report-sheet-brand">
        <div class="report-brand-title">SDR IMS</div>
        <div class="report-brand-sub">Inventory Management System</div>
      </div>
      <div class="report-sheet-meta">
        <div class="report-title">Inventory Report</div>
        <div class="report-date">Generated on <?php echo date('d M Y, h:i A'); ?></div>
      </div>
    </div>

    <div class="rep-stats">
      <div class="rep-stat">
        <div class="rep-big" style="color:var(--primary)"><?php echo (int)$total_skus; ?></div>
        <div class="rep-lbl">Total SKUs</div>
      </div>
      <div class="rep-stat">
        <div class="rep-big" style="color:var(--success)"><?php echo money($cost_value); ?></div>
        <div class="rep-lbl">Cost Value</div>
      </div>
      <div class="rep-stat">
        <div class="rep-big" style="color:var(--teal)"><?php echo money($retail_value); ?></div>
        <div class="rep-lbl">Retail Value</div>
      </div>
      <div class="rep-stat">
        <div class="rep-big" style="color:var(--warning)"><?php echo money($profit); ?></div>
        <div class="rep-lbl">Potential Profit</div>
      </div>
      <div class="rep-stat">
        <div class="rep-big" style="color:var(--danger)"><?php echo (int)$low; ?></div>
        <div class="rep-lbl">Low Stock Items</div>
      </div>
    </div>

    <div class="report-section">
      <div class="report-card">
        <div class="report-card-title">30-Day Movement Trend</div>
        <div class="trend-wrap">
          <canvas id="trendChart"></canvas>
        </div>
      </div>
    </div>

    <div class="report-grid-2">
      <div class="report-card">
        <div class="report-card-title">By Category</div>
        <div class="report-table-wrap report-no-scroll">
          <table class="report-table">
            <thead>
              <tr>
                <th>Category</th>
                <th>Products</th>
                <th>Qty</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              <?php if($catRows && mysqli_num_rows($catRows) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($catRows)): ?>
                  <tr>
                    <td>
                      <span class="cat-dot" style="background:<?php echo h($row['color'] ?: '#4f8ef7'); ?>"></span>
                      <?php echo h($row['category_name']); ?>
                    </td>
                    <td><?php echo (int)$row['products']; ?></td>
                    <td><?php echo (int)$row['qty']; ?></td>
                    <td><?php echo money($row['value']); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="4" class="empty">No category data found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="report-card">
        <div class="report-card-title">Low Stock Items</div>
        <div class="report-table-wrap report-no-scroll">
          <table class="report-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Min</th>
              </tr>
            </thead>
            <tbody>
              <?php if($lowRows && mysqli_num_rows($lowRows) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($lowRows)): ?>
                  <tr>
                    <td>
                      <div class="prod-name"><?php echo h($row['product_name']); ?></div>
                      <div class="prod-sub"><?php echo h($row['sku']); ?> • <?php echo h($row['category_name']); ?></div>
                    </td>
                    <td class="qty-low"><?php echo (int)$row['quantity']; ?></td>
                    <td><?php echo (int)$row['min_stock']; ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="3" class="empty">No low stock items.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="report-note">This report is optimized for print view.</div>
  </div>
</div>
</div>

<style>
.reports-main,.reports-content{overflow:visible !important}
.reports-content{padding-bottom:28px !important}
.report-print-sheet{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:24px;
  padding:18px;
  box-shadow:0 18px 40px rgba(0,0,0,.12);
  overflow:visible !important;
}
.report-sheet-header{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:16px;
  margin-bottom:16px;
}
.report-brand-title{
  font-size:30px;
  font-weight:800;
  letter-spacing:.02em;
}
.report-brand-sub,.report-date,.report-note,.prod-sub{
  color:var(--text-secondary);
}
.report-title{
  font-size:28px;
  font-weight:800;
  text-align:right;
}
.report-date{
  text-align:right;
  margin-top:4px;
}
.report-section{margin-top:16px}
.report-grid-2{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:14px;
  margin-top:14px;
}
.report-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:18px;
  overflow:hidden;
}
.report-card-title{
  padding:12px 14px;
  font-size:15px;
  font-weight:700;
  border-bottom:1px solid var(--border);
  color:var(--text);
}
.trend-wrap{
  height:260px;
  padding:14px;
}
.report-table-wrap{
  overflow:visible !important;
}
.report-no-scroll{
  scrollbar-width:none;
  -ms-overflow-style:none;
}
.report-no-scroll::-webkit-scrollbar{
  width:0 !important;
  height:0 !important;
  display:none !important;
}
.report-table{
  width:100%;
  border-collapse:collapse;
}
.report-table thead th{
  text-align:left;
  font-size:12px;
  letter-spacing:.03em;
  text-transform:uppercase;
  padding:10px 12px;
  border-bottom:1px solid var(--border);
  color:var(--text-secondary);
  background:rgba(148,163,184,.06);
}
.report-table tbody td{
  padding:12px;
  border-bottom:1px solid var(--border);
  vertical-align:top;
}
.report-table tbody tr:last-child td{border-bottom:none}
.qty-low{
  color:var(--danger);
  font-weight:700;
}
.prod-name{
  font-weight:700;
  color:var(--text);
}
.empty{
  text-align:center;
  color:var(--text-secondary);
}
.report-alert-strip{
  margin-bottom:14px !important;
}
html[data-theme="light"] .report-print-sheet,
html[data-theme="light"] .report-card{
  background:#fff !important;
  color:#0f172a !important;
  border-color:#d9e4f2 !important;
}
html[data-theme="light"] .report-brand-sub,
html[data-theme="light"] .report-date,
html[data-theme="light"] .report-note,
html[data-theme="light"] .prod-sub,
html[data-theme="light"] .report-table thead th,
html[data-theme="light"] .empty{
  color:#64748b !important;
}
html[data-theme="light"] .report-card-title,
html[data-theme="light"] .prod-name,
html[data-theme="light"] .report-brand-title,
html[data-theme="light"] .report-title{
  color:#0f172a !important;
}
html[data-theme="light"] .report-table thead th{
  background:#eef4ff !important;
}
@media (max-width: 980px){
  .report-grid-2{grid-template-columns:1fr}
  .report-sheet-header{flex-direction:column}
  .report-title,.report-date{text-align:left}
}
@media print{
  @page{size:A4 portrait; margin:10mm}
  html, body{
    background:#fff !important;
    overflow:visible !important;
  }
  body *{
    visibility:hidden;
  }
  .report-print-sheet, .report-print-sheet *{
    visibility:visible;
  }
  .report-print-sheet{
    position:absolute;
    left:0;
    top:0;
    width:100%;
    margin:0;
    padding:0;
    border:none !important;
    box-shadow:none !important;
    border-radius:0 !important;
    background:#fff !important;
    color:#000 !important;
  }
  .report-card{
    box-shadow:none !important;
    break-inside:avoid;
    page-break-inside:avoid;
  }
  .main, .content, .reports-main, .reports-content{
    margin:0 !important;
    padding:0 !important;
    overflow:visible !important;
  }
  .sidebar, .topbar, .app-footer, .theme-toggle, .bell, .mobile-menu, .btn-sm, .btn-primary, .report-alert-strip{
    display:none !important;
  }
  ::-webkit-scrollbar{
    display:none !important;
    width:0 !important;
    height:0 !important;
  }
}
</style>

<script>
(function(){
  const labels = <?php echo json_encode($trendLabels); ?>;
  const dataIn = <?php echo json_encode($trendIn); ?>;
  const dataOut = <?php echo json_encode($trendOut); ?>;
  const root = document.documentElement;
  const isLight = root.getAttribute('data-theme') === 'light';
  const textColor = isLight ? '#64748b' : '#94a3b8';
  const gridColor = isLight ? 'rgba(15,23,42,0.08)' : 'rgba(255,255,255,0.06)';

  const ctx = document.getElementById('trendChart');
  if (ctx && typeof Chart !== 'undefined') {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'In',
            data: dataIn,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.14)',
            fill: true,
            tension: .35,
            borderWidth: 3,
            pointRadius: 3,
            pointHoverRadius: 4
          },
          {
            label: 'Out',
            data: dataOut,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239,68,68,0.10)',
            fill: true,
            tension: .35,
            borderWidth: 3,
            pointRadius: 3,
            pointHoverRadius: 4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: textColor, boxWidth: 24, usePointStyle: false }
          }
        },
        scales: {
          x: {
            ticks: { color: textColor },
            grid: { color: gridColor }
          },
          y: {
            beginAtZero: true,
            ticks: { color: textColor, precision: 0 },
            grid: { color: gridColor }
          }
        }
      }
    });
  }
})();
</script>
<?php include "partials/footer.php"; ?>

<?php
include "auth.php";
$pageTitle = "Sales Bill";

$id = (int)($_GET['id'] ?? 0);
$sale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, p.product_name, p.sku, p.barcode, p.unit, c.customer_name, c.phone, c.email, c.address
FROM sales s
JOIN products p ON s.product_id=p.id
LEFT JOIN customers c ON s.customer_id=c.id
WHERE s.id=$id LIMIT 1"));

if (!$sale) {
    include "partials/header.php";
    include "partials/sidebar.php";
    echo '<div class="main">';
    include "partials/topbar.php";
    echo '<div class="content"><div class="alert-banner alert-danger">Bill not found.</div></div></div>';
    include "partials/footer.php";
    exit();
}

$issuedByName = $sale['issued_by'] ?? ($_SESSION['name'] ?? 'System User');
$billNo = "SDR-" . str_pad($sale['id'], 5, "0", STR_PAD_LEFT);
$subtotal = (float)($sale['subtotal'] ?: ($sale['quantity'] * $sale['selling_price']));
$discount = (float)($sale['discount'] ?? 0);
$grandTotal = (float)$sale['total'];
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content bill-page-content">

  <div class="bill-actions no-print">
    <a class="btn-sm" href="sale.php"><i class="fa-solid fa-plus"></i> New Sale</a>
    <button class="btn-sm btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Bill</button>
    <a class="btn-sm" href="reports.php"><i class="fa-solid fa-chart-bar"></i> Reports</a>
  </div>

  <section class="pro-bill">
<header class="pro-bill-header">
      <div class="pro-bill-brand with-bill-logo">
        <img class="bill-header-logo" src="assets/img/sdr-ims-bill-logo.png" alt="SDR IMS Logo">
        <div>
          <h1>SDR IMS</h1>
          <p>Inventory Management System</p>
          <span>Sales Bill / Invoice</span>
        </div>
      </div>

      <div class="pro-bill-title-box">
        <h2>INVOICE</h2>
        <p><?php echo h($billNo); ?></p>
      </div>
    </header>

    <div class="pro-bill-info-strip">
      <div>
        <span>Invoice Date</span>
        <strong><?php echo date('d M Y', strtotime($sale['sale_date'])); ?></strong>
      </div>
      <div>
        <span>Invoice Time</span>
        <strong><?php echo date('h:i A', strtotime($sale['sale_date'])); ?></strong>
      </div>
      <div>
        <span>Reference</span>
        <strong><?php echo h($sale['reference_no']); ?></strong>
      </div>
      <div>
        <span>Issued By</span>
        <strong><?php echo h($issuedByName); ?></strong>
      </div>
    </div>

    <div class="pro-bill-parties">
      <div class="party-card">
        <h3>Bill To</h3>
        <p class="party-name"><?php echo h($sale['customer_name'] ?: 'Cash Customer'); ?></p>
        <?php if(!empty($sale['phone'])): ?><p><i class="fa-solid fa-phone"></i> <?php echo h($sale['phone']); ?></p><?php endif; ?>
        <?php if(!empty($sale['email'])): ?><p><i class="fa-solid fa-envelope"></i> <?php echo h($sale['email']); ?></p><?php endif; ?>
        <?php if(!empty($sale['address'])): ?><p><i class="fa-solid fa-location-dot"></i> <?php echo h($sale['address']); ?></p><?php endif; ?>
      </div>

      <div class="party-card">
        <h3>Issued From</h3>
        <p class="party-name">SDR IMS</p>
        <p><i class="fa-solid fa-user"></i> <?php echo h($issuedByName); ?></p>
        <p><i class="fa-solid fa-boxes-stacked"></i> Inventory Management System</p>
      </div>
    </div>

    <table class="pro-bill-table">
      <thead>
        <tr>
          <th style="width:46px">#</th>
          <th>Item Description</th>
          <th>SKU / Barcode</th>
          <th class="text-center">Qty</th>
          <th class="text-right">Unit Price</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-center">1</td>
          <td>
            <strong><?php echo h($sale['product_name']); ?></strong>
            <small>Unit: <?php echo h($sale['unit']); ?></small>
          </td>
          <td>
            <span class="print-sku"><?php echo h($sale['sku']); ?></span>
            <?php if(!empty($sale['barcode'])): ?><small><?php echo h($sale['barcode']); ?></small><?php endif; ?>
          </td>
          <td class="text-center"><?php echo h($sale['quantity']); ?></td>
          <td class="text-right"><?php echo money($sale['selling_price']); ?></td>
          <td class="text-right"><strong><?php echo money($subtotal); ?></strong></td>
        </tr>
      </tbody>
    </table>

    <div class="pro-bill-bottom">
      <div class="bill-note-box">
        <h4>Note</h4>
        <p>This is a computer-generated bill from SDR IMS. Please keep it for your records.</p>
        <p class="thank-you">Thank you for your business!</p>
      </div>

      <div class="pro-bill-totals">
        <div class="total-line">
          <span>Subtotal</span>
          <strong><?php echo money($subtotal); ?></strong>
        </div>
        <div class="total-line">
          <span>Discount</span>
          <strong><?php echo money($discount); ?></strong>
        </div>
        <div class="total-line grand">
          <span>Grand Total</span>
          <strong><?php echo money($grandTotal); ?></strong>
        </div>
      </div>
    </div>

    <div class="pro-bill-signatures">
      <div>
        <span></span>
        <p>Customer Signature</p>
      </div>
      <div>
        <span></span>
        <p>Authorized Signature</p>
      </div>
    </div>

    <footer class="pro-bill-footer">
      <p>Generated by SDR IMS • <?php echo date('d M Y, h:i A'); ?></p>
    </footer>
  </section>
</div>
</div>
<?php include "partials/footer.php"; ?>

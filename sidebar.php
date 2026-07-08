<aside class="sidebar">
  <div class="sidebar-brand">
    <img class="brand-logo" src="assets/img/sdr-ims-logo.png" alt="SDR IMS Logo">
    <div>
      <span class="brand-name">SDR IMS</span>
      <span class="brand-sub">Inventory Management System</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-label">Main</div>
    <a class="nav-item <?php echo activePage('dashboard.php'); ?>" href="dashboard.php"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>
    <a class="nav-item <?php echo activePage('products.php'); ?>" href="products.php"><i class="fa-solid fa-boxes-stacked"></i><span>Products</span></a>
    <a class="nav-item <?php echo activePage('categories.php'); ?>" href="categories.php"><i class="fa-solid fa-tags"></i><span>Categories</span></a>
    <a class="nav-item <?php echo activePage('suppliers.php'); ?>" href="suppliers.php"><i class="fa-solid fa-truck"></i><span>Suppliers</span></a>
    <a class="nav-item <?php echo activePage('customers.php'); ?>" href="customers.php"><i class="fa-solid fa-user-group"></i><span>Customers</span></a>

    <div class="nav-label">Operations</div>
    <a class="nav-item <?php echo activePage('stock.php'); ?>" href="stock.php"><i class="fa-solid fa-arrow-right-arrow-left"></i><span>Stock Movements</span></a>
    <a class="nav-item <?php echo activePage('purchase.php'); ?>" href="purchase.php"><i class="fa-solid fa-file-invoice"></i><span>Purchase / Stock In</span></a>
    <a class="nav-item <?php echo activePage('sale.php'); ?>" href="sale.php"><i class="fa-solid fa-cart-shopping"></i><span>Sale / Stock Out</span></a>
    <a class="nav-item <?php echo activePage('reports.php'); ?>" href="reports.php"><i class="fa-solid fa-chart-bar"></i><span>Reports</span></a>
    <a class="nav-item <?php echo activePage('low_stock.php'); ?>" href="low_stock.php"><i class="fa-solid fa-triangle-exclamation"></i><span>Low Stock</span></a>

    <div class="nav-label">Administration</div>
    <a class="nav-item <?php echo activePage('users.php'); ?>" href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
    <a class="nav-item <?php echo activePage('profile.php'); ?>" href="profile.php"><i class="fa-solid fa-user-gear"></i><span>My Profile</span></a>
    <a class="nav-item <?php echo activePage('developer_contact.php'); ?>" href="developer_contact.php"><i class="fa-solid fa-headset"></i><span>Developer Contact</span></a>
  </nav>

  <div class="sidebar-footer">
    <?php
    $currentProfilePic = null;
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $picRes = @mysqli_query($conn, "SELECT profile_picture FROM users WHERE id=$uid LIMIT 1");
        if ($picRes && mysqli_num_rows($picRes) === 1) {
            $picRow = mysqli_fetch_assoc($picRes);
            $currentProfilePic = $picRow['profile_picture'] ?? null;
        }
    }
    ?>
    <?php if($currentProfilePic): ?>
      <img class="profile-avatar-img" src="<?php echo h($currentProfilePic); ?>" alt="Profile Picture">
    <?php else: ?>
      <div class="u-avatar"><?php echo strtoupper(substr($_SESSION['name'] ?? 'AD', 0, 2)); ?></div>
    <?php endif; ?>
    <div style="flex:1;min-width:0">
      <span class="user-name"><?php echo h($_SESSION['name'] ?? 'System Admin'); ?></span>
      <span class="user-role"><?php echo h(ucfirst($_SESSION['role'] ?? 'Admin')); ?></span>
    </div>
    <a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket logout-icon"></i></a>
  </div>
</aside>

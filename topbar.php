<header class="topbar">
  <button class="mobile-menu" onclick="document.body.classList.toggle('sidebar-open')"><i class="fa-solid fa-bars"></i></button>
  <div class="topbar-title"><?php echo h($pageTitle ?? 'Dashboard'); ?></div>
  <form class="search-wrap" method="GET" action="products.php">
    <i class="fa-solid fa-search"></i>
    <input type="text" name="search" placeholder="Search products, SKU..." value="<?php echo h($_GET['search'] ?? ''); ?>">
  </form>
  <button type="button" class="theme-toggle" onclick="toggleThemeMode()" aria-label="Toggle dark and light mode" title="Dark / Light Mode">
    <span class="theme-toggle-track">
      <span class="theme-toggle-stars"></span>
      <span class="theme-toggle-thumb">
        <i class="fa-solid fa-moon theme-icon theme-icon-moon"></i>
        <i class="fa-solid fa-sun theme-icon theme-icon-sun"></i>
      </span>
    </span>
    <span class="theme-toggle-text">Dark</span>
  </button>
  <a class="btn-sm btn-primary" href="purchase.php"><i class="fa-solid fa-plus"></i> Stock In</a>
  <a class="bell" href="low_stock.php"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></a>
</header>

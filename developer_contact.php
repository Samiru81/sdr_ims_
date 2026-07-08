<?php
include "auth.php";
$pageTitle = "Developer Contact";
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
  <div class="developer-contact-card no-image">
    <div class="developer-icon-large"><i class="fa-solid fa-headset"></i></div>
    <h1>Developer Contact</h1>
    <p class="text-muted">For SDR IMS technical support, hosting help, customization, or bug fixing, contact the developer.</p>

    <div class="contact-grid">
      <a class="contact-box" href="tel:0719957871">
        <i class="fa-solid fa-phone"></i>
        <div>
          <span>Contact No</span>
          <strong>0719957871</strong>
        </div>
      </a>

      <a class="contact-box" href="mailto:s.d.randiwela@gmail.com?subject=SDR IMS Support Request">
        <i class="fa-solid fa-envelope"></i>
        <div>
          <span>E-mail</span>
          <strong>s.d.randiwela@gmail.com</strong>
        </div>
      </a>
    </div>

    <div class="developer-footer-note">
      <i class="fa-solid fa-code"></i>
      Developed by <strong>Samiru Dinushan</strong>
    </div>
  </div>
</div>
</div>
<?php include "partials/footer.php"; ?>

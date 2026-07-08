<?php
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
(function(){
  try{
    var saved = localStorage.getItem('sdr-ims-theme');
    var theme = saved || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) ? 'light' : 'dark');
    document.documentElement.setAttribute('data-theme', theme);
  }catch(e){document.documentElement.setAttribute('data-theme','dark');}
})();
</script>
<title><?php echo h($pageTitle); ?> - SDR IMS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<link rel="stylesheet" href="assets/css/style.css?v=online_ims_light_user_lowstock_1">
<script src="assets/js/password-security.js?v=1"></script>
<script src="assets/js/theme-toggle.js?v=1" defer></script>
</head>
<body>

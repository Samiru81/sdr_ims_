<?php
include "auth.php";
$pageTitle = "Categories";
$msg = "";

if(isset($_POST['add'])){
    $name = clean($_POST['category_name']);
    $desc = clean($_POST['description']);
    $color = clean($_POST['color']);
    $icon = clean($_POST['icon']);
    mysqli_query($conn, "INSERT INTO categories(category_name,description,color,icon) VALUES('$name','$desc','$color','$icon')");
    $msg = "Category added.";
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM categories WHERE id=$id");
    header("Location: categories.php");
    exit();
}
$rows = mysqli_query($conn, "SELECT c.*, COUNT(p.id) products FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.id DESC");
?>
<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<div class="main">
<?php include "partials/topbar.php"; ?>
<div class="content">
<?php if($msg): ?><div class="alert-banner alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
  <div class="card" style="width:320px;flex-shrink:0">
    <div class="card-hdr"><h3>Add Category</h3></div>
    <div class="card-body">
      <form method="POST">
        <div class="fg"><label>Name *</label><input name="category_name" placeholder="Category name" required></div>
        <div class="fg"><label>Description</label><textarea name="description" rows="3" placeholder="Optional description"></textarea></div>
        <div class="form-grid">
          <div class="fg"><label>Color</label><input type="color" name="color" value="#4f8ef7"></div>
          <div class="fg"><label>Icon</label><select name="icon"><option value="fa-box">box</option><option value="fa-laptop">laptop</option><option value="fa-chair">chair</option><option value="fa-flask">flask</option></select></div>
        </div>
        <button class="btn-sm btn-primary" name="add" style="width:100%;padding:9px"><i class="fa-solid fa-plus"></i> Add Category</button>
      </form>
    </div>
  </div>
  <div class="card" style="flex:1;min-width:500px">
    <div class="card-hdr"><h3>All Categories</h3></div>
    <table>
      <thead><tr><th>Color</th><th>Name</th><th>Description</th><th>Products</th><th>Action</th></tr></thead>
      <tbody>
      <?php while($r=mysqli_fetch_assoc($rows)): ?>
      <tr>
        <td><span style="display:inline-block;width:18px;height:18px;border-radius:4px;background:<?php echo h($r['color']); ?>"></span></td>
        <td><b><?php echo h($r['category_name']); ?></b></td>
        <td><small class="text-muted"><?php echo h($r['description']); ?></small></td>
        <td><span class="badge badge-i"><?php echo $r['products']; ?></span></td>
        <td><a class="btn-sm btn-danger" href="?delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a></td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
<?php include "partials/footer.php"; ?>

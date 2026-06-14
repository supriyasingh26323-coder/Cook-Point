<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}
include("../php/config.php");

if (isset($_POST['add'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $image       = mysqli_real_escape_string($conn, $_POST['image']);
    $is_available = 1;

    $sql = "INSERT INTO menu_items (name, description, price, category, image, is_available)
            VALUES ('$name','$description','$price','$category','$image','$is_available')";

    if (mysqli_query($conn, $sql)) {
        $success = "Product Added Successfully!";
    } else {
        $error = "Database Error. Please try again.";
    }
}

$categories = mysqli_query($conn, "SELECT name FROM categories WHERE name != 'All'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product - Cook Point</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <nav class="navbar">
    <a href="dashboard.php" class="logo">Cook <span>Point</span></a>
    <ul class="nav-links">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="add-product.php" class="active">Add Product</a></li>
      <li><a href="view-orders.php">Orders</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <section class="form-page">
    <div class="form-box">
      <h2>Add Menu Item</h2>
      <p class="sub">Add new food product to menu</p>

      <?php if (isset($success)) { ?>
        <div class="alert alert-success" style="display:block;"><?php echo $success; ?></div>
      <?php } ?>
      <?php if (isset($error)) { ?>
        <div class="alert alert-danger" style="display:block;"><?php echo $error; ?></div>
      <?php } ?>

      <form method="POST">
        <div class="form-group">
          <label>Food Name</label>
          <input type="text" name="name" placeholder="e.g. Paneer Burger" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="2" placeholder="Short description of the item"></textarea>
        </div>
        <div class="form-group">
          <label>Price (₹)</label>
          <input type="number" name="price" placeholder="e.g. 149" required>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category" style="width:100%;padding:10px;border:1px solid #e0e0e0;border-radius:8px;font-size:14px;">
            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
              <option value="<?php echo $cat['name']; ?>"><?php echo $cat['name']; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group">
          <label>Image Filename</label>
          <input type="text" name="image" placeholder="e.g. burger.jpg" required>
        </div>
        <button type="submit" name="add" class="btn-primary btn-block">Add Item</button>
      </form>
    </div>
  </section>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}
include("../php/config.php");

$result = mysqli_query($conn, "
    SELECT o.id, o.total_amount, o.delivery_address, o.status, o.created_at, u.name AS customer
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Cook Point</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <nav class="navbar">
    <a href="dashboard.php" class="logo">Cook <span>Point</span></a>
    <ul class="nav-links">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="add-product.php">Add Product</a></li>
      <li><a href="view-orders.php" class="active">Orders</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <section class="section">
    <h2 class="section-title">Customer <span>Orders</span></h2>

    <div class="cart-container">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Address</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) == 0) { ?>
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#999;">No orders yet.</td></tr>
          <?php } ?>
          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><strong>#<?php echo $row['id']; ?></strong></td>
              <td><?php echo htmlspecialchars($row['customer'] ?? 'Guest'); ?></td>
              <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
              <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
              <td><span style="background:#ff5722;color:white;padding:3px 10px;border-radius:20px;font-size:12px;"><?php echo $row['status']; ?></span></td>
              <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>

</body>
</html>

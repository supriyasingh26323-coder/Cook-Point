<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}
include("../php/config.php");

$total_orders  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_amount) FROM orders"))[0];
$total_users   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_items   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM menu_items WHERE is_available=1"))[0];
$total_revenue = $total_revenue ? number_format($total_revenue, 2) : '0.00';

// Recent 5 orders
$recent = mysqli_query($conn, "
    SELECT o.id, o.total_amount, o.created_at, u.name AS customer
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Cook Point Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-wrap { max-width: 1100px; margin: 0 auto; padding: 40px 30px; }
    .welcome-bar {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: white;
      padding: 28px 35px;
      border-radius: 18px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .welcome-bar h2 { font-size: 22px; margin-bottom: 4px; }
    .welcome-bar p  { color: #aaa; font-size: 14px; }
    .welcome-bar span { color: #ff5722; }
    .recent-title { font-size: 20px; font-weight: 700; margin: 35px 0 15px; color: #1a1a2e; }
    .recent-table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
    .recent-table th { background: #1a1a2e; color: white; padding: 14px 20px; text-align: left; font-size: 14px; }
    .recent-table td { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .badge-pending { background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .quick-links { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 25px; }
    .quick-link {
      background: white;
      border: 2px solid #f0f0f0;
      padding: 14px 22px;
      border-radius: 12px;
      text-decoration: none;
      color: #333;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s;
    }
    .quick-link:hover { border-color: #ff5722; color: #ff5722; transform: translateY(-2px); }
  </style>
</head>
<body style="background:#f5f5f5;">

  <nav class="navbar">
    <a href="dashboard.php" class="logo">Cook <span>Point</span></a>
    <ul class="nav-links">
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="add-product.php">Add Product</a></li>
      <li><a href="view-orders.php">Orders</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <div class="admin-wrap">

    <!-- Welcome Bar -->
    <div class="welcome-bar">
      <div>
        <h2>Welcome back, <span>Admin</span> 👋</h2>
        <p>Here's what's happening at Cook Point today — <?php echo date('d F Y'); ?></p>
      </div>
      <a href="reports.php" class="btn-primary" style="white-space:nowrap;">📄 View Report</a>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats">
      <div class="stat-card">
        <div class="stat-icon orange">📦</div>
        <div class="stat-info">
          <h3><?php echo $total_orders; ?></h3>
          <p>Total Orders</p>
          <a href="view-orders.php" class="stat-link">View all →</a>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-info">
          <h3>₹<?php echo $total_revenue; ?></h3>
          <p>Total Revenue</p>
          <a href="reports.php" class="stat-link">Full report →</a>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div class="stat-info">
          <h3><?php echo $total_users; ?></h3>
          <p>Registered Users</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple">🍔</div>
        <div class="stat-info">
          <h3><?php echo $total_items; ?></h3>
          <p>Menu Items</p>
          <a href="add-product.php" class="stat-link">Add new →</a>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <p class="recent-title">Recent Orders</p>
    <table class="recent-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($recent) == 0) { ?>
          <tr><td colspan="5" style="text-align:center;padding:25px;color:#999;">No orders yet.</td></tr>
        <?php } ?>
        <?php while ($row = mysqli_fetch_assoc($recent)) { ?>
          <tr>
            <td><strong>#<?php echo $row['id']; ?></strong></td>
            <td><?php echo htmlspecialchars($row['customer'] ?? 'Guest'); ?></td>
            <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
            <td><span class="badge-pending">Pending</span></td>
            <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <!-- Quick Links -->
    <div class="quick-links">
      <a href="add-product.php" class="quick-link">➕ Add New Product</a>
      <a href="view-orders.php" class="quick-link">📋 All Orders</a>
      <a href="reports.php" class="quick-link">📄 Generate Report</a>
    </div>

  </div>

</body>
</html>

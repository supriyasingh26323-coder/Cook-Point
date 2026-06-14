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
$total_items   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM menu_items"))[0];
$total_revenue = $total_revenue ? number_format($total_revenue, 2) : '0.00';

$orders = mysqli_query($conn, "
    SELECT o.id, o.total_amount, o.status, o.created_at, u.name AS customer, u.phone
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
  <title>Reports - Cook Point Admin</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-wrap   { max-width: 1100px; margin: 0 auto; padding: 40px 30px; }
    .report-head  {
      text-align: center;
      padding: 25px;
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: white;
      border-radius: 18px;
      margin-bottom: 30px;
    }
    .report-head h2 { font-size: 22px; margin-bottom: 5px; }
    .report-head p  { color: #aaa; font-size: 13px; }

    .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 18px; margin-bottom: 35px; }
    .stat-box {
      background: white;
      border-radius: 14px;
      padding: 22px 20px;
      border-left: 4px solid #ff5722;
      box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    }
    .stat-box h3 { font-size: 28px; font-weight: 800; color: #ff5722; margin-bottom: 4px; }
    .stat-box p  { font-size: 13px; color: #666; }

    .print-btn {
      background: #ff5722;
      color: white;
      border: none;
      padding: 11px 28px;
      border-radius: 50px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(255,87,34,0.35);
      transition: all 0.3s;
    }
    .print-btn:hover { background: #e64a19; transform: translateY(-2px); }

    .report-table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
    .report-table th { background: #1a1a2e; color: white; padding: 15px 20px; text-align: left; font-size: 14px; }
    .report-table td { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .report-table tr:last-child td { border-bottom: none; }
    .badge { background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    @media print {
      .navbar, .print-btn { display: none !important; }
      body { background: white !important; }
    }
  </style>
</head>
<body style="background:#f5f5f5;">

  <nav class="navbar">
    <a href="dashboard.php" class="logo">Cook <span>Point</span></a>
    <ul class="nav-links">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="add-product.php">Add Product</a></li>
      <li><a href="view-orders.php">Orders</a></li>
      <li><a href="reports.php" class="active">Reports</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <div class="admin-wrap">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
      <h2 style="font-size:24px;color:#1a1a2e;">Business <span style="color:#ff5722;">Report</span></h2>
      <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

    <div class="report-head">
      <h2>Cook Point — Business Summary Report</h2>
      <p>Generated on: <?php echo date('d F Y, h:i A'); ?></p>
    </div>

    <div class="stat-row">
      <div class="stat-box">
        <h3><?php echo $total_orders; ?></h3>
        <p>📦 Total Orders</p>
      </div>
      <div class="stat-box">
        <h3>₹<?php echo $total_revenue; ?></h3>
        <p>💰 Total Revenue</p>
      </div>
      <div class="stat-box">
        <h3><?php echo $total_users; ?></h3>
        <p>👥 Registered Users</p>
      </div>
      <div class="stat-box">
        <h3><?php echo $total_items; ?></h3>
        <p>🍔 Menu Items</p>
      </div>
    </div>

    <h3 style="margin-bottom:15px;color:#1a1a2e;font-size:18px;">All Orders Detail</h3>
    <table class="report-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer Name</th>
          <th>Phone</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($orders) == 0) { ?>
          <tr><td colspan="6" style="text-align:center;padding:25px;color:#999;">No orders found.</td></tr>
        <?php } ?>
        <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
          <tr>
            <td><strong>#<?php echo $row['id']; ?></strong></td>
            <td><?php echo htmlspecialchars($row['customer'] ?? 'Guest'); ?></td>
            <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
            <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
            <td><span class="badge"><?php echo $row['status']; ?></span></td>
            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <p style="text-align:center;margin-top:30px;color:#bbb;font-size:13px;">
      © <?php echo date('Y'); ?> Cook Point | BCA Project Report
    </p>

  </div>
</body>
</html>

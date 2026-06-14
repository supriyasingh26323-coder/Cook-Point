<?php
include("../php/config.php");

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = true;
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Username or Password";
        }
    } else {
        $error = "Invalid Username or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Cook Point</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="form-page">

  <div class="form-box">
    <h2>🔐 Admin Login</h2>
    <p class="sub">Cook Point Admin Panel</p>

    <?php if (isset($error)) { ?>
      <div class="alert alert-danger" style="display:block;"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter admin username" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" name="login" class="btn-primary btn-block">Login to Admin</button>
    </form>
  </div>

</body>
</html>

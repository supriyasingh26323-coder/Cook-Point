<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id  = $_SESSION['user_id'];
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $total    = mysqli_real_escape_string($conn, $_POST['total']);
    $cart     = json_decode($_POST['cart'], true);

    $order_sql = "INSERT INTO orders (user_id, total_amount, delivery_address) VALUES ('$user_id','$total','$address')";
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        foreach ($cart as $item) {
            $item_id  = (int)$item['id'];
            $qty      = (int)$item['quantity'];
            $price    = (float)$item['price'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES ('$order_id','$item_id','$qty','$price')");
        }
        echo "success";
    } else {
        echo "error";
    }
}
?>
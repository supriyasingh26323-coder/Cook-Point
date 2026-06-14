<?php
include 'config.php';
header('Content-Type: application/json');

$type = isset($_GET['type']) ? $_GET['type'] : 'menu';

// Return categories list
if ($type === 'categories') {
    $result = mysqli_query($conn, "SELECT name, icon FROM categories ORDER BY id");
    $cats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cats[] = $row;
    }
    echo json_encode($cats);
    exit();
}

// Return menu items
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

if ($category && $category != 'All') {
    $sql = "SELECT * FROM menu_items WHERE category='$category' AND is_available=1";
} else {
    $sql = "SELECT * FROM menu_items WHERE is_available=1";
}

$result = mysqli_query($conn, $sql);
$items  = [];

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode($items);
?>

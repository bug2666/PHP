<?php
if (!isset($_SESSION['customer_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo '<p> Giỏ hàng trống.</p>';
    return;
}
?>

<h1>Thanh toán</h1>
<p>Khách hàng: <?php echo htmlspecialchars($_SESSION['customer_name']); ?></p>
<p>Email: <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>
<p>Trang xử lí đơn hàng.</p>
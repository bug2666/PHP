<?php
$orderId = $_SESSION['last_order_id'] ?? 0;

if (!$orderId) {
    echo '<p>Khong tim thay don hang vua tao.</p>';
    return;
}

unset($_SESSION['last_order_id']);
?>

<h1>Đặt hàng thành công</h1>
<p>Mã đơn hàng của bạn là: <strong>#<?php echo (int) $orderId; ?></strong></p>
<p><a href="index.php">Quay về trang chủ</a></p>

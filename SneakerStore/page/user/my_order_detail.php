<?php
if (!isset($_SESSION['customer_id'])) {
    header('Location: /PHP/SneakerStore/index.php?page=login');
    exit;
}

$customerId = (int) $_SESSION['customer_id'];
$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    echo '<p>Đơn hàng không hợp lệ.</p>';
    return;
}

$sqlOrder = "
    SELECT id, receiver_name, receiver_phone, receiver_address, total_amount, order_status, created_at
    FROM orders
    WHERE id = ? AND customer_id = ?
    LIMIT 1
";

$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bind_param('ii', $orderId, $customerId);
$stmtOrder->execute();

$orderResult = $stmtOrder->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    echo '<p>Bạn không có quyền xem đơn hàng này hoặc đơn hàng không tồn tại.</p>';
    return;
}

$sqlItems = "
    SELECT product_id, product_name, price, quantity, subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
";

$stmtItems = $conn->prepare($sqlItems);
$stmtItems->bind_param('i', $orderId);
$stmtItems->execute();

$itemResult = $stmtItems->get_result();
?>

<h1>Chi tiết đơn hàng #<?php echo (int) $order['id']; ?></h1>

<p>Người nhận: <?php echo htmlspecialchars($order['receiver_name']); ?></p>
<p>Số điện thoại: <?php echo htmlspecialchars($order['receiver_phone']); ?></p>
<p>Địa chỉ: <?php echo htmlspecialchars($order['receiver_address']); ?></p>
<p>Trạng thái: <strong><?php echo htmlspecialchars($order['order_status']); ?></strong></p>
<p>Ngày tạo: <?php echo htmlspecialchars($order['created_at']); ?></p>
<p>Tổng tiền: <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</p>

<h2>Sản phẩm trong đơn hàng</h2>

<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr>
        <th>ID sản phẩm</th>
        <th>Tên sản phẩm</th>
        <th>Đơn giá</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
    </tr>

    <?php while ($item = $itemResult->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int) $item['product_id']; ?></td>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
            <td><?php echo (int) $item['quantity']; ?></td>
            <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> VND</td>
        </tr>
    <?php endwhile; ?>
</table>

<p>
    <a href="index.php?page=my_orders">Quay lại đơn hàng của tôi</a>
</p>
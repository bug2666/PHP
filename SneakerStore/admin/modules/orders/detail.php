<?php
$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmtOrder = $conn->prepare("
    SELECT orders.*, customers.full_name AS customer_name, customers.email AS customer_email
    FROM orders
    LEFT JOIN customers ON orders.customer_id = customers.id
    WHERE orders.id = ?
    LIMIT 1
");
$stmtOrder->bind_param("i", $orderId);
$stmtOrder->execute();

$orderResult = $stmtOrder->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    echo '<p>Đơn hàng không tồn tại.</p>';
    return;
}

$stmtItems = $conn->prepare("
    SELECT id, product_id, product_name, price, quantity, subtotal
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();

$itemResult = $stmtItems->get_result();
?>


<h1>Chi tiết đơn hàng #<?php echo (int) $order['id']; ?></h1>

<p>Khách hàng: <?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></p>
<p>Email: <?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></p>
<p>Người nhận: <?php echo htmlspecialchars($order['receiver_name']); ?></p>
<p>Số điện thoại: <?php echo htmlspecialchars($order['receiver_phone']); ?></p>
<p>Địa chỉ: <?php echo htmlspecialchars($order['receiver_address']); ?></p>
<p>Tổng tiền: <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</p>
<p>Trạng thái hiện tại: <strong><?php echo htmlspecialchars($order['order_status']); ?></strong></p>
<p>Ngày tạo: <?php echo htmlspecialchars($order['created_at']); ?></p>

<h2>Cập nhật trạng thái</h2>
<form action="modules/orders/updateStatus.php" method="post">
    <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
    <select name="order_status">
        <?php $currentStatus = $order['order_status']; ?>
        <option value="pending" <?php if ($currentStatus === 'pending') echo 'selected'; ?>>pending</option>
        <option value="confirmed" <?php if ($currentStatus === 'confirmed') echo 'selected'; ?>>confirmed</option>
        <option value="shipping" <?php if ($currentStatus === 'shipping') echo 'selected'; ?>>shipping</option>
        <option value="completed" <?php if ($currentStatus === 'completed') echo 'selected'; ?>>completed</option>
        <option value="cancelled" <?php if ($currentStatus === 'cancelled') echo 'selected'; ?>>cancelled</option>

    </select>
    <button type="submit">Cập nhật</button>
</form>

<h2>Sản phẩm trong đơn hàng</h2>

<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr>
        <th>ID</th>
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

<p><a href="index.php?action=orders&query=list">Quay lại danh sách đơn hàng</a></p>
<?php
$sql = "
    SELECT orders.id, orders.customer_id, orders.receiver_name, orders.receiver_phone,
           orders.receiver_address, orders.total_amount, orders.order_status, orders.created_at,
           customers.full_name AS customer_name, customers.email AS customer_email
    FROM orders
    LEFT JOIN customers ON orders.customer_id = customers.id 
    ORDER BY orders.id DESC
";

$result = $conn->query($sql);
?>

<h1>Danh sách đơn hàng</h1>

<?php if ($result && $result->num_rows > 0): ?>
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Email</th>
            <th>Người nhận</th>
            <th>Số điện thoại</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>

        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo (int) $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($order['receiver_name']); ?></td>
                <td><?php echo htmlspecialchars($order['receiver_phone']); ?></td>
                <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                <td>
                    <a href="index.php?action=orders&query=detail&id=<?php echo $order['id']; ?>">Xem chi tiet</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>Chưa có đơn hàng nào.</p>
<?php endif; ?>
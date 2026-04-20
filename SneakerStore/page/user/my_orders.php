<?php
if (!isset($_SESSION['customer_id'])) {
    header('Location: /PHP/SneakerStore/index.php?page=login');
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$sql = "
    SELECT id, receiver_name, receiver_phone, receiver_address, total_amount, order_status, created_at
    FROM orders
    WHERE customer_id = ?
    ORDER BY id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $customerId);
$stmt->execute();

$result = $stmt->get_result();
?>

<h1>Đơn hàng của tôi</h1>

<?php if ($result->num_rows === 0): ?>
    <p>Bạn chưa có đơn hàng nào.</p>
<?php else: ?>
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>Mã đơn</th>
            <th>Người nhận</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>

        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo (int) $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['receiver_name']); ?></td>
                <td><?php echo htmlspecialchars($order['receiver_phone']); ?></td>
                <td><?php echo htmlspecialchars($order['receiver_address']); ?></td>
                <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                <td>
                    <a href="index.php?page=my_order_detail&id=<?php echo (int) $order['id']; ?>">Xem chi tiết</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
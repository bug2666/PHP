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


<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr>
        <th>Sản phẩm</th>
        <th>Số lượnng</th>
        <th>Đơn giá</th>
        <th>Tạm tính</th>
    </tr>
    <?php $total = 0; ?>
    <?php foreach ($cart as $item): ?>
        <?php $subtotal = $item['price'] * $item['quantity'];
              $total += $subtotal;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo (int) $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
            <td><?php echo number_format($total, 0, ',', '.'); ?> VND</td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4" style="text-align:right;">
            <strong>Tổng tiền: <?php echo number_format($subtotal, 0, ',', '.'); ?> VND</strong>
        </td>
    </tr>
</table>

<h2>Thông tin nhận hàng</h2>

<form action="/PHP/SneakerStore/page/Checkout/checkout_action.php" method="post">
    <p>
        <label>Tên người nhận</label><br>
        <input type="text" name="receiver_name" value="<?php echo htmlspecialchars($_SESSION['customer_name']); ?>">
    </p>
    <p>
        <label>Số điện thoại</label><br>
        <input type="text" name="receiver_phone">
    </p>
    <p>
        <label>Địa chỉ nhận hàng</label><br>
        <input type="text" name="receiver_address">
    </p>
    <button type="submit">Xác nhận đặt hàng</button>
</form>
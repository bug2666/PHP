<?php
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>
<h1>giỏ hàng</h1>
<?php if (empty($cart)): ?>

    <p>giỏ hàng đang trống</p>

<?php else: ?>
    <table border="1" cellspacing="0" cellpadding="10" width="100%">
        <tr>
            <th>Sản phẩm</th>
            <th>Hình ảnh</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Tạm tính</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($cart as $item): ?>
            <?php
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;

            ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($item['name']); ?><br>
                    <small><?php echo htmlspecialchars($item['sku']); ?></small>
                </td>
                <td>
                    <img src="uploads/products/<?php echo htmlspecialchars($item['image']); ?>" width="100" alt="">
                </td>
                <td>
                    <?php echo number_format($item['price'], 0, ',', '.'); ?> VND
                </td>
                <td>
                    <form action="page/cart/cart_action.php" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="productid" value="<?php echo $item['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                        <button type="submit">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <?php echo number_format($subtotal, 0, ',', '.'); ?> VND
                </td>
                <td>
                    <form action="page/cart/cart_action.php" method="post">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="6" style="text-align:right;">
                <strong>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VND</strong>
            </td>
        </tr>
    </table>
    <p>
    <form action="page/cart/cart_action.php" method="post">
        <input type="hidden" name="action" value="clear">
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
        <button type="submit">Xóa tất cả</button>
    </form>
    </p>
    <p>
        <a href="index.php?page=checkout">Tiến hành thanh toán</a>
    </p>
<?php endif; ?>
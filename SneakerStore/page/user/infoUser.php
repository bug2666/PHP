<?php
if (!isset($_SESSION['customer_id'])) {
    header("Location: /PHP/SneakerStore/index.php?page=login");
    exit;
}

$id = (int) $_SESSION['customer_id'];

$sql = 'SELECT * FROM customers WHERE id = ? LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo '<p>Không tim thấy thông tin người dùng.</p>';
    return;
}
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<?php if ($success !== ''): ?>
    <script>
        alert('<?php echo $success; ?>');
    </script>
<?php endif; ?>

<form action="page/user/infoUser_action.php" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <p>Họ và tên</p>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($customer['full_name']); ?>">

    <p>Email</p>
    <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">

    <p>Số điện thoại</p>
    <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($customer['phone']); ?>">

    <p>Địa chỉ</p>
    <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>">

    <p>
        <a href="index.php?page=my_orders">Xem đơn hàng của tôi </a>
    </p>
    <button type="submit">Cập nhật</button>
</form>
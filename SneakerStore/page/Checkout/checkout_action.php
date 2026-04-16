<?php
/* xử lí đặt hàng */
session_start();
require_once __DIR__ . '/..//..//config/database.php';
if (!isset($_SESSION['customer_id'])) {
    header('LocationmL: ../index.php?page=login');
    exit;
}
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: ../index.php?page=cart');
    exit;
}
$receiverName = trim($_POST['receiver_name'] ?? '');
$receiverPhone = trim($_POST['receiver_phone'] ?? '');
$receiverAddress = trim($_POST['receiver_address'] ?? '');
if ($receiverName == '' || $receiverPhone == '' || $receiverAddress == '') {
    $_SESSION['checkout_error'] = 'Vui lòng nhập đầy đủ thông tin để nhận hàng.';
    header('location: ../index.php?page=checkout');
    exit;
}
$customerId = (int) $_SESSION['customer_id'];
$totalAmount = 0;
foreach ($cart as $item) {
    $totalAmount = $item['price'] * $item['quantity'];
}

$conn->begin_transaction(); //  bắt đầu gói xử lí
try {
    $status = 'pending';
    $sql =
        "INSERT INTO orders (
                customer_id,
                receiver_name,
                receiver_phone,
                receiver_address,
                total_amount,
                order_status
            ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmtOrder = $conn->prepare($sql);

    $stmtOrder->bind_param(
        "isssds",
        $customerId,
        $receiverName,
        $receiverPhone,
        $receiverAddress,
        $totalAmount,
        $status
    );

    if (!$stmtOrder->execute()) {
        throw new Exception("không thể tạo đơn hàng"); // nhảy xuống cath để dừng
    }
    $orderId = $conn->insert_id; // lấy id vừa được tạo bởi lệnh insert gần nhất
    $sqlProduct = 'SELECT id, name, price, quantity FROM products WHERE id = ? AND status = 1 LIMIT 1';
    $stmtProduct = $conn->prepare($sqlProduct);

    $sqlItem = 'INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)';
    $stmtItem = $conn->prepare($sqlItem);

    $sqlUpdateStock = 'UPDATE products SET quantity = quantity - ? WHERE id = ?';
    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);

    foreach ($cart as $item) {
        $productId = $item['id'];
        $Buyquantity = $item['quantity'];
        $stmtProduct->bind_param('i', $productId);
        $stmtProduct->execute();

        $result = $stmtProduct->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            throw new Exception('Sản phẩm không tồn tại');
        }
        if ((int)$product['quantity'] < (int) $Buyquantity) {
            throw new Exception('sản phẩm này không đủ số lượng tồn kho.');
        }

        $prductName = $product['name'];
        $price = (float) $product['price'];
        $subtotal = $price * $Buyquantity;

        $stmtItem->bind_param(
            "iisdid",
            $orderId,
            $productId,
            $prductName,
            $price,
            $Buyquantity,
            $subtotal
        );

        if (!$stmtItem->execute()) {
            throw new Exception('Không thể lưu chi tiết đơn hàng.');
        }

        $stmtUpdateStock->bind_param("ii", $buyQuantity, $productId);

        if (!$stmtUpdateStock->execute()) {
            throw new Exception('Không thể cập nhật số lượng tồn kho.');
        }
    }

    $orderId;
    $conn->commit(); // xác nhận dữ liệu đã được đổ vào DB
    unset($_SESSION['cart']);
    $_SESSION['last_order_id'] = $orderId; // lưu lại mã đơn hàng để hiển thị ở trang thành công
    
    header('Location: ../../index.php?page=order_success');
    exit;
} catch (Exception $e) {
    $conn->rollback(); // quay về
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: ../index.php?page=checkout');
    exit;
}

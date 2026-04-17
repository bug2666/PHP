<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: /PHP/SneakerStore/index.php?page=login');
    exit;
}

$id = (int) $_SESSION['customer_id'];

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phoneNumber = trim($_POST['phoneNumber'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($full_name === '' || $email === '') {
    echo 'Vui lòng nhập đầy đủ thông tin.';
    exit;
}

$sql = "UPDATE customers 
        SET full_name = ?, email = ?, phone = ?, address = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $full_name, $email, $phoneNumber, $address, $id);

if ($stmt->execute()) {
    $_SESSION['customer_name'] = $full_name;
    $_SESSION['customer_email'] = $email;

    header('Location: /PHP/SneakerStore/index.php?page=infoUser');
    $_SESSION['success'] = 'Cập nhật thành công';
} else {
    echo 'Cập nhật thất bại.';
}
?>

<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../login.php');
    exit;
}

$orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
$orderStatus = trim($_POST['order_status'] ?? '');

$allowedStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];

if ($orderId <= 0 || !in_array($orderStatus, $allowedStatuses, true)) {
    header('Location: ../../index.php?action=orders&query=list');
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $orderStatus, $orderId);
$stmt->execute();

header('Location: ../../index.php?action=orders&query=detail&id='. $orderId);
exit;
<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('location: ../../login.php');
    exit;
}
$action = $_POST['action'] ?? $_GET['action'];

if ($action === 'create') {
    $name = trim($_POST['name'] ?? "");
    $sort_order = trim($_POST['sort_order'] ?? "");
    $status = (int)($_POST['status'] ?? 1);

    $sql = 'insert into categories(name, sort_order, status) value (?,?,?)';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $name, $sort_order, $status);
    $stmt->execute();

    // $result = $stmt->get_result();
    // $category = $result->fetch_assoc();
    header('Location: ../../index.php?action=categories&query=list');
    exit;
}
if ($action === 'edit') {
    $id = trim($_POST['id'] ?? "");
    $name = trim($_POST['name'] ?? "");
    $sort_order = trim($_POST['sort_order'] ?? "");
    $status = (int)($_POST['status'] ?? 1);

    $sql = 'update categories set name = ?, sort_order = ?, status = ? where id = ?';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siii', $name, $sort_order, $status, $id);
    $stmt->execute();

    // $result = $stmt->get_result();
    // $category = $result->fetch_assoc();
    header('Location: ../../index.php?action=categories&query=list');
    exit;
}
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;

    $sql = 'delete from categories where id = ?';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // $result = $stmt->get_result();
    // $category = $result->fetch_assoc();
    header('Location: ../../index.php?action=categories&query=list');
    exit;
}
header('Location: ../../index.php?action=categories&query=list');
exit;

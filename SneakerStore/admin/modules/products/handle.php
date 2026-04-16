<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';
$uploadDir = __DIR__ . '/../../../uploads/products/';

if (!isset($_SESSION['admin_id'])) {
    header('location: ../../login.php');
    exit;
}
$action = $_POST['action'] ?? $_GET['action'];

if ($action === 'create') {
    $name = trim($_POST['name'] ?? ""); 
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $sku = trim($_POST['sku'] ?? "");
    $price = $_POST['price'] ?? 0;
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $summary = trim($_POST['summary'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = (int) ($_POST['status'] ?? 1);
    $imageName = '';

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }


    $stmt = $conn->prepare("
        INSERT INTO products (category_id, name, sku, price, quantity, summary, description, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issdisssi", $categoryId, $name, $sku, $price, $quantity, $summary, $description, $imageName, $status);
    $stmt->execute();

    header('Location: ../../index.php?action=products&query=list');
    exit;
}
if ($action === 'edit') {
    $id = (int) ($_POST['id'] ?? 0);
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $summary = trim($_POST['summary'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = (int) ($_POST['status'] ?? 1);
    $oldImage = trim($_POST['old_image'] ?? '');

    $imageName = $oldImage;

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newImageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $newImageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            die('Khong the luu file upload.');
        }

        $imageName = $newImageName;

        if ($oldImage !== '' && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }
    }


    $stmt = $conn->prepare("
        UPDATE products
        SET category_id = ?, name = ?, sku = ?, price = ?, quantity = ?, summary = ?, description = ?, image = ?, status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("issdisssii", $categoryId, $name, $sku, $price, $quantity, $summary, $description, $imageName, $status, $id);
    $stmt->execute();

    header('Location: ../../index.php?action=products&query=list');
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_GET['id'] ?? 0);

    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product && !empty($product['image']) && file_exists($uploadDir . $product['image'])) {
        unlink($uploadDir . $product['image']);
    }

    $stmtDelete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmtDelete->bind_param("i", $id);
    $stmtDelete->execute();

    header('Location: ../../index.php?action=products&query=list');
    exit;
}

header('Location: ../../index.php?action=products&query=list');
exit;

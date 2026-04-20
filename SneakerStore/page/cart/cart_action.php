<?php
session_start();
require_once __DIR__. '/..//..//config/database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if($action === 'add'){
    $productid = isset($_POST['productid']) ? (int)$_POST['productid'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $quantity = max(1,$quantity);

    $query = "select id, name, sku, price, quantity, image from  products where id = ? and status = 1 limit 1";
    $sttm = $conn -> prepare($query);
    $sttm -> bind_param('i', $productid);

    $sttm -> execute();
    $result = $sttm -> get_result();
    $product = $result->fetch_assoc();

    if($product){
        $stock = $product['quantity'];
        $quantity = min($quantity,$stock);

        if($quantity >0 ){
            if(isset($_SESSION['cart'][$productid])){
                $newquantity = $_SESSION['cart'][$productid]['quantity'] + $quantity; // cập nhật lại số lượng
                $_SESSION['cart'][$productid]['quantity'] = min($newquantity, $stock);
                // $productId = 6;
                // $_SESSION['cart'][$productId] = [
                //     'name' => 'Converse Chuck 70',
                //     'quantity' => 1
                // ];

            }
            else // xử lí case sản phẩm chưa có trong giở
            {
                $_SESSION['cart'][$productid]=[
                    'id' => (int) $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => (float)$product['price'],
                    'image' => $product['image'],
                    'stock' => $stock,
                    'quantity' => $quantity
                ];

            }
        }
    }
    header('location: ../..//index.php?page=cart');
    exit;
}
if($action === 'update'){
    $productId = isset($_POST['productid']) ? (int) $_POST['productid'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $quantity = max(1, $quantity);

    if (isset($_SESSION['cart'][$productId])) {
        $stock = (int) $_SESSION['cart'][$productId]['stock'];
        $_SESSION['cart'][$productId]['quantity'] = min($quantity, $stock);
    }

    header('location: ../../index.php?page=cart');
    exit;
}
if($action === 'remove'){
    $productid = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if(isset($_SESSION['cart'][$productid])) unset($_SESSION['cart'][$productid]);
   
    header('location: ../../index.php?page=cart');
    exit;
}
if ($action === 'clear') {
    unset($_SESSION['cart']);
    header('location: ../../index.php?page=cart');
    exit;
}
header('location: ../../index.php');
exit;

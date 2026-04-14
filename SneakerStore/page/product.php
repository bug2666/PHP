<?php
$productID = (int)$_GET['id'];
$sql = "
    select products.* , categories.name
    from products left join categories on products.id = categories.id
    where products.id = ? and products.status = 1
    limit 1
";
$sttm  = $conn->prepare($sql); // chuẩn bị 
$sttm->bind_param('i', $productID);  // nạp vào 
$sttm->execute(); // thực thi trả về true false

$result = $sttm->get_result();
$product = $result->fetch_assoc();

if (!$product) echo 'sản phầm không tồn tại';

$name = htmlspecialchars($product['name']);
$image = htmlspecialchars($product['image']);
$sku = htmlspecialchars($product['sku']);
$categoryName = htmlspecialchars($product['category_name'] ?? 'Chua co');
$price = number_format((float) $product['price'], 0, ',', '.');
$quantity = (int) $product['quantity'];
$summary = htmlspecialchars($product['summary']);
$description = htmlspecialchars($product['description']);

?>
<h1><?php echo $name  ?></h1>
<div class="product-detail">
    <img src="uploads/products/<?php echo $image; ?>" alt="<?php echo $name; ?>" width="300">

    <p>Mã sản phẩm: <?php echo $sku; ?></p>
    <p>Danh mục: <?php echo $categoryName; ?></p>
    <p>Giá: <?php echo $price; ?> VND</p>
    <p>Số lượng: <?php echo $quantity; ?></p>
    <p>Mô tả ngắn: <?php echo $summary; ?></p>
    <p>Mô tả chi tiết: <?php echo $description; ?></p>
    <p><a href="index.php?page=cart">Thêm vào giỏ hàng</a></p>
</div>
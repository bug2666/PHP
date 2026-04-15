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
$product_id = $product['id'];

?>
<h1><?php echo $name  ?></h1>
<div class="product-detail">
    <img src="uploads/products/<?php echo $image; ?>" alt="<?php echo $name; ?>" width="100" height="100"/>

    <p>Mã sản phẩm: <?php echo $sku; ?></p>
    <p>Danh mục: <?php echo $categoryName; ?></p>
    <p>Giá: <?php echo $price; ?> VND</p>
    <p>Số lượng: <?php echo $quantity; ?></p>
    <p>Mô tả ngắn: <?php echo $summary; ?></p>
    <p>Mô tả chi tiết: <?php echo $description; ?></p>

    <form action="./page/cart/cart_action.php" method="post">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="productid" value="<?php echo $product_id; ?>">
        <input type="number" name="quantity" value="1" min="1" max="<?php echo $quantity; ?>">
        <button type="submit">Thêm vào giỏ hàng</button>
    </form>



</div>
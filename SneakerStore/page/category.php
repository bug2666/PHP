<?php
$categoryId = (int) ($_GET['id'] ?? 0);

if ($categoryId <= 0) {
    echo '<p>Danh mục không hợp lệ.</p>';
    return;
}

$categorySql = "
    SELECT id, name
    FROM categories
    WHERE id = ? AND status = 1
    LIMIT 1
";

$categoryStmt = $conn->prepare($categorySql);
$categoryStmt->bind_param("i", $categoryId);
$categoryStmt->execute();

$result = $categoryStmt->get_result();
$category = $result->fetch_assoc(); // dùng ở đây vì chỉ lấy 1 dữ liệu. còn đặt trong while thì lấy từ đầu tới cuối

if (!$category) {
    echo '<p>Danh mục không tồn tại.</p>';
    return;
}

$productSql = "
    SELECT id, name, price, image
    FROM products
    WHERE category_id = ? AND status = 1
    ORDER BY id DESC
";

$productStmt = $conn->prepare($productSql);
$productStmt->bind_param("i", $categoryId);
$productStmt->execute();

$productResult = $productStmt->get_result();
?>

<h1>Danh mục: <?php echo htmlspecialchars($category['name']); ?></h1>

<?php if ($productResult->num_rows === 0): ?>
    <p>Danh mục này chưa có sản phẩm.</p>
<?php else: ?>
    <div class="product-list">
        <?php while ($product = $productResult->fetch_assoc()): ?>
            <?php
            $id = (int) $product['id'];
            $name = htmlspecialchars($product['name']);
            $image = htmlspecialchars($product['image']);
            $price = number_format((float) $product['price'], 0, ',', '.');
            ?>
            <div class="product-item">
                <a href="index.php?page=product&id=<?php echo $id; ?>">
                    <img src="uploads/products/<?php echo $image; ?>" alt="<?php echo $name; ?>" width="200">
                    <h3><?php echo $name; ?></h3>
                    <p>Giá: <?php echo $price; ?> VND</p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
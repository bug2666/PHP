<?php
$sql = "SELECT products.id, products.name, products.price, products.quantity, products.image, categories.name  AS category_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        WHERE products.status = 1
        ORDER BY products.id DESC";
$result = $conn->query($sql);
?>
<?php
if ($result && $result->num_rows > 0) { ?>
    <div class="product-list">
        <?php
        while ($products = $result->fetch_assoc()) {
            $id = (int) $products['id'];
            $name =  htmlspecialchars($products['name']);
            $image = htmlspecialchars($products['image']);
            $price = (float)($products['price']);
            $categoryName = htmlspecialchars($products['category_name'] ?? 'chưa có thông tin');
            $quantity = $products['quantity'];
        ?>
            <?php
            if ($quantity> 0) {
            ?>
                <div class="product-item">
                    <h1><?php $image ?></h1>
                    <a href="index.php?page=product&id=<?php echo $id; ?>" style="text-decoration: none;">
                        <img src="/PHP/SneakerStore/uploads/products/<?php echo $image ?>" alt="<?php echo $name ?>">
                    </a>

                    <h3><?php echo $name; ?></h3>
                    <p>Danh mục<code></code>: <?php echo $categoryName; ?></p>
                    <p>Giá: <?php echo $price; ?> VND</p>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
<?php

if (!isset($_SESSION['admin_id'])) {
    header('Location: /PHP/SneakerStore/admin/login.php');
    exit;
}
$id = (int) $_GET['id'] ?? 0;

$sql = 'select * from products where id = ? limit 1';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();

$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo " sản phẩm này không tồn tại";
    return;
}

$categoryResultSQL = 'select id, name from categories where status = 1 order by sort_order asc, id desc';
$categoryResult = $conn->query($categoryResultSQL);
?>
<form action="modules/products/handle.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?php echo (int) $product['id']; ?>">
    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($product['image']); ?>">


    <p>
        <label>Tên sản phẩm</label> <br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']) ?>">
    </p>
    <p>
        <label>SKU</label><br>
        <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku']) ?>">
    </p>
    <p>
        <label>price</label><br>
        <input type="number" name="price" min=0 value="<?php echo (float)$product['price'] ?>">
    </p>
    <p>
        <label>Số lượng</label><br>
        <input type="number" name="quantity" min=0 value="<?php echo (int)$product['quantity'] ?>">
    </p>
    <p>
        <label>Mô tả ngắn</label><br>
        <input type="text" name="summary" value="<?php echo htmlentities($product['summary']) ?>">
    </p>
    <p>
        <label>Mô tả chi tiết</label><br>
        <input type="text" name="description" value="<?php echo htmlentities($product['description']) ?>">
    </p>

    <p>
        <label>Danh mục</label><br>
        <select name="category_id">
            <?php while ($cate = $categoryResult->fetch_assoc()): ?>
                <option value="<?php echo $cate['id']; ?>"
                    <?php echo $cate['id'] == $product['category_id'] ? 'selected' : ''; ?>>

                    <?php echo htmlspecialchars($cate['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        <label>Hình hiện tại</label><br>
        <?php if (!empty($product['image'])): ?>
            <img src="/PHP/SneakerStore/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" width="120" alt="">
        <?php endif; ?>
    </p>

    <p>
        <label>Chọn ảnh mới</label><br>
        <input type="file" name=image>
    </p>

    <p>
        <label>Trạng thái</label><br>
        <select name="status">
            <option value="1" <?php echo (int) $product['status'] === 1 ? 'selected' : "" ?>>Hiện</option>
            <option value="0" <?php echo (int) $product['status'] === 0 ? 'selected' : "" ?>> Ẩn </option>
        </select>
    </p>

    <button type="submit">Lưu</button>
</form>
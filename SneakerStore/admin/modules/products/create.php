<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: /PHP/SneakerStore/admin/login.php');
    exit;
}
$sql = "select id, name from categories where status = 1";
$categoryResult = $conn->query($sql);

?>
<form action="modules/products/handle.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">
    <input type="hidden" name="id" value="<?php echo (int) $category['id'];?>">

    <p>
        <label>Tên sản phẩm</label>
        <input type="text" name="name">
    </p>
    <p>
        <label>SKU</label>
        <input type="text" name="sku">
    </p>
    <p>
        <label>price</label>
        <input type="number" name="price" min=0>
    </p>
    <p>
        <label>Số lượng</label>
        <input type="number" name="quantity" min=0>
    </p>
    <p>
        <label>Mô tả ngắn</label>
        <input type="text" name="summary">
    </p>
    <p>
        <label>Mô tả chi tiết</label>
        <input type="text" name="description">
    </p>

    <p>
        <label>Danh mục</label>
        <select name="category_id">
            <?php
            while ($cate = $categoryResult->fetch_assoc()): ?>
                <option value="<?php echo $cate['id'] ?>"> <?php echo htmlspecialchars($cate['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        <label>Hình ảnh</label>
        <input type="file" name=image>
    </p>

    <p>
        <label>Trạng thái</label><br>
        <select name="status">
            <option value="1">Hiện</option>
            <option value="0">Ẩn</option>
        </select>
    </p>

    <button type="submit">Lưu</button>
</form>
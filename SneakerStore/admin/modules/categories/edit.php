<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo '<p>Danh mục không tồn tại.</p>';
    return;
}
?>

<h1>Sửa danh mục</h1>

<form action="modules/categories/handle.php" method="post">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?php echo (int) $category['id']; ?>">

    <p>
        <label>Tên danh mục</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>">
    </p>

    <p>
        <label>Thứ tự</label><br>
        <input type="number" name="sort_order" value="<?php echo (int) $category['sort_order']; ?>">
    </p>

    <p>
        <label>Trạng thái</label><br>
        <select name="status">
            <option value="1" <?php echo (int) $category['status'] === 1 ? 'selected' : ''; ?>>Hiện</option>
            <option value="0" <?php echo (int) $category['status'] === 0 ? 'selected' : ''; ?>>Ẩn</option>
        </select>
    </p>

    <button type="submit">Cập nhật</button>
</form>
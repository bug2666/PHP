<?php

$sql = "
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.id DESC
";
$result = $conn->query($sql);

?>


<h1>Danh sách sản phẩm</h1>
<p><a href="index.php?action=products&query=create">Thêm sản phẩm</a></p>
<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>SKU</th>
        <th>Danh mục</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Hình</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int) $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['sku']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name'] ?? 'Chua co'); ?></td>
            <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VND</td>
            <td><?php echo (int) $row['quantity']; ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <img src="../uploads/products/<?php echo htmlspecialchars($row['image']); ?>" width="80" alt="">
                <?php endif; ?>
            </td>
            <td><?php echo (int) $row['status'] === 1 ? 'Hiện' : 'Ẩn'; ?></td>
            <td>
                <a href="index.php?action=products&query=edit&id=<?php echo $row['id']; ?>">Sửa</a>
                |
                <a href="modules/products/handle.php?action=delete&id=<?php echo $row['id']; ?>" onclick=" return confirm('Xóa sản phẩm này?');">Xóa</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
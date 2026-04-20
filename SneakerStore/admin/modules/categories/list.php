<?php
$result = $conn->query("SELECT * FROM categories ORDER BY sort_order ASC, id DESC");
?>

<h1>Danh sách danh mục</h1>
<p><a href="index.php?action=categories&query=create">Thêm danh mục</a></p>

<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr>
        <th>ID</th>
        <th>Tên danh mục</th>
        <th>Thứ tự</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo (int) $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo (int) $row['sort_order']; ?></td>
            <td><?php echo (int) $row['status'] === 1 ? 'Hiện' : 'Ẩn'; ?></td>
            <td>
                <a href="index.php?action=categories&query=edit&id=<?php echo $row['id']; ?>">Sửa</a>
                <a href="modules/categories/handle.php?action=delete&id=<?php echo $row['id']; ?>" onclick=" return confirm('Xóa danh mục này?');">Xóa</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
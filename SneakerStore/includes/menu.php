<?php require_once __DIR__ . '/config/app.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <?php
    $sql = "select name, id from categories where status = 1 order by sort_order ASC, id DESC";
    $result = $conn->query($sql);
    ?>
    <nav>
        <a href="<?php echo APP_URL; ?>/index.php">Trang chủ</a>
        <a href="<?php echo APP_URL; ?>/index.php?page=cart">Giỏ hàng</a>
        <?php if (isset($_SESSION['customer_name'])): ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=infoUser">
                <span>Xin chào, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
            </a>

            <a href="<?php echo APP_URL; ?>/index.php?page=logout">Đăng xuất</a>
        <?php else: ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=login">Đăng nhập</a>
            <a href="<?php echo APP_URL; ?>/index.php?page=register">Đăng kí</a>
        <?php endif; ?>

        <a href="<?php echo APP_URL; ?>/index.php?page=contact">Liên hệ</a>
    </nav>
    <h3>Danh mục sản phẩm</h3>
    <ul>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) {
                //assoc sẽ trả về dữ liệu dạng mảng dạng key value
                echo '<li>';
                echo '<a href="index.php?page=category&id=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a>';
                echo '</a>';
                echo '</li>';
            }
        } else {
            echo '<li>Chưa có danh mục</li>';
        }
        echo "</ul>";
        ?>
    </ul>

</body>

</html>
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
        <a href="/PHP/SneakerStore/index.php">Trang chủ</a>
        <a href="../index.php?page=cart">Giỏ hàng</a>
        <a href="../index.php?page=login">Đăng nhập</a>
        <a href="../index.php?page=register">Đăng kí</a>
        <a href="../index.php?page=contact">Liên hệ</a>
    </nav>
    <h3>Danh mục sản phẩm</h3>
    <ul>
        <?php
        if ($result && $result->num_rows > 0){
            while ($category = $result ->fetch_assoc()){
                //assoc sẽ trả về dữ liệu dạng mảng dạng key value
                echo '<li>';
                    echo '<a href="index.php?page=category&id='.$category['id'].'">'.htmlspecialchars($category['name']).'</a>';
                    echo '</a>';
                echo '</li>';

            }
        } 
        else {
            echo '<li>Chưa có danh mục</li>';
        }
        echo "</ul>";
        ?>
    </ul>

</body>

</html>
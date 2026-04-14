<?php
$productID =(int)$_GET['id'];
$sql = "
    select product.* , categories.name
    from product join categories on product.id = categories.id
    where status = 1
";
$sttm = $conn->query($sql);

?>


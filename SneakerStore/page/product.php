<?php
$productID =(int)$_GET['id'];
$sql = "
    select product.* , categories.name
    from product join categories on product.id = categories.id
    where product.id = ? and products.status = 1
    limit 1
";
$result  = $conn->query($sql);
?>
<div class="products-list">

</div>


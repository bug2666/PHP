<?php
session_start();
include __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div>
        <?php include __DIR__ . '/includes/header.php'; ?>
        <?php include __DIR__ . '/includes/menu.php'; ?>
        <?php include __DIR__ . '/page/main.php'; ?>
        <?php include __DIR__ . '/includes/footer.php'; ?>

    </div>
</body>
</html>
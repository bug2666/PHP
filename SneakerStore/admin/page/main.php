<!-- router admin -->
<?php
$action = $_GET['action'] ?? 'dashboard';
$query = $_GET['query'] ?? '';

if ($action === 'orders' && $query === 'list') {
    include __DIR__ . '/../modules/orders/list.php';
} elseif ($action === 'orders' && $query === 'detail') {
    include __DIR__ . '/../modules/orders/detail.php';
} else {
    include __DIR__ . '/dashboard.php';
}
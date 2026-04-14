<?php
$action = $_GET['action'] ?? 'dashboard';
$query = $_GET['query'] ?? '';

if ($action === 'categories' && $query === 'list') {
    include __DIR__ . '/../modules/categories/list.php';
} elseif ($action === 'categories' && $query === 'create') {
    include __DIR__ . '/../modules/categories/create.php';
} elseif ($action === 'categories' && $query === 'edit') {
    include __DIR__ . '/../modules/categories/edit.php';
} elseif ($action === 'products' && $query === 'list') {
    include __DIR__ . '/../modules/products/list.php';
} elseif ($action === 'products' && $query === 'create') {
    include __DIR__ . '/../modules/products/create.php';
} elseif ($action === 'products' && $query === 'edit') {
    include __DIR__ . '/../modules/products/edit.php';
} elseif ($action === 'orders' && $query === 'list') {
    include __DIR__ . '/../modules/orders/list.php';
} elseif ($action === 'contacts' && $query === 'list') {
    include __DIR__ . '/../modules/contacts/list.php';
} else {
    include __DIR__ . '/dashboard.php';
}

<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'cart':
        include __DIR__ . '/cart/cart.php';
        break;
    case 'category':
        include __DIR__ . '/category.php';
        break;
    case 'checkout':
        include __DIR__ . '/checkout.php';
        break;
    case 'login':
        include __DIR__ . '/login.php';
        break;
    case 'register':
        include __DIR__ . '/register.php';
        break;
    case 'product':
        include __DIR__ . '/product.php';
        break;
    case 'contact':
        include __DIR__ . '/contact.php';
        break;
    case 'logout':
        include __DIR__ . '/logout.php';
        break;
    default:
        include __DIR__ . '/home.php';
}

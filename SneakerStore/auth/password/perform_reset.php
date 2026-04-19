<!-- Người dùng mở link reset có token
Vào trang reset_password.php
Nhập password và confirm_password
Form gửi sang perform_reset.php
File này:
    kiểm tra token có còn hợp lệ không
    kiểm tra 2 mật khẩu có khớp không
    rồi mới cập nhật mật khẩu thật -->

<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/verify_token.php';

$token = trim($_POST['token'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

if ($token === '' || $password === '' || $confirmPassword === '') {
    $_SESSION['reset_password_error'] = 'Vui lòng nhập đầy đủ thông tin.';
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['reset_password_error'] = 'Mật khẩu xác nhận không khớp.';
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}

$resetRow = findValidPasswordResetToken($conn, $token);

if (!$resetRow) {
    $_SESSION['reset_password_error'] = 'Link đặt lại mật khẩu không khớp hoặc đã hết hạn.';
    header('Location: ../../index.php?page=forgot_password');
    exit;
}

/*
 * Buoi 3 chua update password that.
 * Buoi 4-5 moi xu ly tiep.
 */
$_SESSION['reset_password_message'] = 'Token hop le. Buoi tiep theo se cap nhat mat khau moi.';
header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
exit;
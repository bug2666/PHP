

<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/verify_token.php';

$token = trim($_POST['token'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

if ($token === '' || $password === '' || $confirmPassword === '') {
    $_SESSION['reset_password_error'] = 'Vui lòng nhập đủ thông tin';
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['reset_password_error'] = 'Mật khẩu xác nhận không đúng';
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['reset_password_error'] = 'Mật khẩu phải có ít nhất 6 kí tự.';
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}

$resetRow = findValidPasswordResetToken($conn, $token);

if (!$resetRow) {
    $_SESSION['reset_password_error'] = 'Link đặt lại khẩu không hợp lệ hoặc hết hạn.';
    header('Location: ../../index.php?page=forgot_password');
    exit;
}

$customerId = (int) $resetRow['customer_id'];
$resetId = (int) $resetRow['id'];

$passwordHash = md5($password);

$conn->begin_transaction();

try {
    $stmtUpdateCustomer = $conn->prepare("
        UPDATE customers
        SET password = ?
        WHERE id = ?
    ");
    $stmtUpdateCustomer->bind_param("si", $passwordHash, $customerId);

    if (!$stmtUpdateCustomer->execute()) {
        throw new Exception('Không thể cập nhật mật khẩu mới.');
    }

    $stmtMarkUsed = $conn->prepare("
        UPDATE password_resets
        SET used_at = NOW()
        WHERE id = ?
    ");
    $stmtMarkUsed->bind_param("i", $resetId);

    if (!$stmtMarkUsed->execute()) {
        throw new Exception('Không thể đánh dấu token đã sử dụng.');
    }

    $conn->commit();

    $_SESSION['forgot_password_message'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại';
    header('Location: ../../index.php?page=login');
    exit;
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['reset_password_error'] = $e->getMessage();
    header('Location: ../../index.php?page=reset_password&token=' . urlencode($token));
    exit;
}


// <!-- Người dùng mở link reset có token
// Vào trang reset_password.php
// Nhập password và confirm_password
// Form gửi sang perform_reset.php
// File này:
//     kiểm tra token có còn hợp lệ không
//     kiểm tra 2 mật khẩu có khớp không
//     rồi mới cập nhật mật khẩu thật -->
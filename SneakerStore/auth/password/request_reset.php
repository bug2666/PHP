

<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../mail/mailer.php';
require_once __DIR__ . '/../mail/templates/reset_password.php';

$email = trim($_POST['email'] ?? '');

unset($_SESSION['forgot_password_message']);
unset($_SESSION['forgot_password_error']);


if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['forgot_password_error'] = 'Email khong hop le.';
    header('Location: ../../index.php?page=forgot_password');
    exit;
}


$genericMessage = 'Kiểm tra email nếu mail đúng !.';

$stmt = $conn->prepare("SELECT id, email, full_name FROM customers WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();

$_SESSION['forgot_password_message'] = $genericMessage;

if (!$customer) {
    header('Location: ../../index.php?page=forgot_password');
    exit;
}

$rawToken = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $rawToken);
$expiresAt = date('Y-m-d H:i:s', time() + 1800);

$customerId = (int) $customer['id'];
$customerName = $customer['full_name'] ?? 'Customer';

$stmtDelete = $conn->prepare("
    DELETE FROM password_resets
    WHERE customer_id = ? AND used_at IS NULL
");
$stmtDelete->bind_param("i", $customerId);
$stmtDelete->execute();

$stmtInsert = $conn->prepare("
    INSERT INTO password_resets (customer_id, email, token_hash, expires_at)
    VALUES (?, ?, ?, ?)
");
$stmtInsert->bind_param("isss", $customerId, $email, $tokenHash, $expiresAt);
$stmtInsert->execute();

$resetLink = APP_URL . '/index.php?page=reset_password&token=' . urlencode($rawToken);


$subject = 'Đặt lại mật khẩu - SneakerStore';
$htmlBody = buildResetPasswordEmail($resetLink, $email);

sendMail($email, $customerName, $subject, $htmlBody);

header('Location: ../../index.php?page=forgot_password');
exit;

/*
kiểm tra email
tìm user
tạo token
băm token
lưu vào bảng password_resets
gửi link reset qua email */

<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

$rawInput = file_get_contents('php://input'); // lấy các giá trị mà _post hoặc _get không lấy được
$data = json_decode($rawInput, true); // chuyển dữ liệu về dạng mảng

$idToken = $data['credential'] ?? ''; // lấy token mà gg trả về khi login 

if ($idToken === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing Google credential.'
    ]);
    exit;
}

$client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);
$payload = $client->verifyIdToken($idToken);

if (!$payload) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Google ID token.'
    ]);
    exit;
}

$googleSub = $payload['sub'] ?? '';
$email = $payload['email'] ?? '';
$fullName = $payload['name'] ?? 'Google User';
$avatarUrl = $payload['picture'] ?? null;
$emailVerified = !empty($payload['email_verified']) ? 1 : 0;

if ($googleSub === '' || $email === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Google token payload is incomplete.'
    ]);
    exit;
}
// kiểm tra theo google_sub

$stmt = $conn->prepare("
    SELECT id, full_name, email
    FROM customers
    WHERE google_sub = ?
    LIMIT 1
");
$stmt->bind_param('s', $googleSub);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if ($customer) {
    $customerId = (int) $customer['id'];

    $stmt = $conn->prepare("
        UPDATE customers
        SET full_name = ?, avatar_url = ?, email_verified = ?, last_login_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param('ssii', $fullName, $avatarUrl, $emailVerified, $customerId);
    $stmt->execute();

    $_SESSION['customer_id'] = $customerId;
    $_SESSION['customer_name'] = $fullName;
    $_SESSION['customer_email'] = $email;

    echo json_encode([
        'success' => true,
        'redirect' => GOOGLE_LOGIN_SUCCESS_REDIRECT
    ]);
    exit;
}


// kiểm tra xem khách hàng đã có tài khoản chưa 1. kiểm tra theo email
$sqlEmail = 'select id, full_name, email from customers where email = ? limit 1';

$stmt = $conn->prepare($sqlEmail);
$stmt->bind_param('s', $email);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if ($customer) {
    $customerId = $customer['id'];
    $auth_provider = 'google'; // báo cáo hệ thống rằng đang login bằng google
    $stmt = $conn->prepare("
        UPDATE customers
        SET google_sub = ?, auth_provider = ?, avatar_url = ?, email_verified = ?, last_login_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param('sssii', $googleSub, $authProvider, $avatarUrl, $emailVerified, $customerId);
    $stmt->execute();

    $_SESSION['customer_id'] = $customerId;
    $_SESSION['customer_name'] = $customer['full_name'];
    $_SESSION['customer_email'] = $email;

    echo json_encode([
        'success' => true,
        'redirect' => GOOGLE_LOGIN_SUCCESS_REDIRECT
    ]);
    exit;
}
// trường hợp chưa có tài khoảnn
$authProvider = 'google';
$nullPassword = null;
$phone = '';
$address = '';

$stmt = $conn->prepare("
    INSERT INTO customers (
        full_name,
        email,
        password,
        phone,
        address,
        google_sub,
        auth_provider,
        avatar_url,
        email_verified,
        last_login_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    'ssssssssi',
    $fullName,
    $email,
    $nullPassword,
    $phone,
    $address,
    $googleSub,
    $authProvider,
    $avatarUrl,
    $emailVerified
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Could not create customer from Google account.'
    ]);
    exit;
}

$customerId = $conn->insert_id;

$_SESSION['customer_id'] = $customerId;
$_SESSION['customer_name'] = $fullName;
$_SESSION['customer_email'] = $email;

echo json_encode([
    'success' => true,
    'redirect' => GOOGLE_LOGIN_SUCCESS_REDIRECT
]);
exit;

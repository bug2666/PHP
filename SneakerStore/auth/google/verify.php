<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $databaseFile = __DIR__ . '/../../config/database.php';
    $configFile = __DIR__ . '/config.php';
    $autoloadFile = __DIR__ . '/../../vendor/autoload.php';

    if (!file_exists($databaseFile)) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Missing config/database.php'
        ]);
    }

    if (!file_exists($configFile)) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Missing auth/google/config.php'
        ]);
    }

    if (!file_exists($autoloadFile)) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Missing vendor/autoload.php'
        ]);
    }

    require_once $databaseFile;
    require_once $configFile;
    require_once $autoloadFile;

    if (!class_exists('Google_Client')) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Google_Client class not found.'
        ]);
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Invalid JSON body.'
        ]);
    }

    $idToken = trim((string) ($data['credential'] ?? ''));

    if ($idToken === '') {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Missing Google credential.'
        ]);
    }

    $client = new Google_Client([
        'client_id' => GOOGLE_CLIENT_ID
    ]);

    $payload = $client->verifyIdToken($idToken);

    if (!$payload) {
        jsonResponse(401, [
            'success' => false,
            'message' => 'Invalid Google ID token.'
        ]);
    }

    $googleSub = trim((string) ($payload['sub'] ?? ''));
    $email = trim((string) ($payload['email'] ?? ''));
    $fullName = trim((string) ($payload['name'] ?? 'Google User'));
    $avatarUrl = trim((string) ($payload['picture'] ?? ''));
    $emailVerified = !empty($payload['email_verified']) ? 1 : 0;
    $redirect = defined('GOOGLE_LOGIN_SUCCESS_REDIRECT')
        ? GOOGLE_LOGIN_SUCCESS_REDIRECT
        : '/index.php';

    if ($googleSub === '' || $email === '') {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Google token payload is incomplete.'
        ]);
    }

    $stmt = $conn->prepare("
        SELECT id, full_name, email
        FROM customers
        WHERE google_sub = ?
        LIMIT 1
    ");

    if (!$stmt) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Prepare failed (find by google_sub): ' . $conn->error
        ]);
    }

    $stmt->bind_param('s', $googleSub);

    if (!$stmt->execute()) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Execute failed (find by google_sub): ' . $stmt->error
        ]);
    }

    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        $customerId = (int) $customer['id'];

        $stmt = $conn->prepare("
            UPDATE customers
            SET full_name = ?, avatar_url = ?, email_verified = ?, last_login_at = NOW()
            WHERE id = ?
        ");

        if (!$stmt) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Prepare failed (update existing google_sub user): ' . $conn->error
            ]);
        }

        $stmt->bind_param('ssii', $fullName, $avatarUrl, $emailVerified, $customerId);

        if (!$stmt->execute()) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Execute failed (update existing google_sub user): ' . $stmt->error
            ]);
        }

        $_SESSION['customer_id'] = $customerId;
        $_SESSION['customer_name'] = $fullName;
        $_SESSION['customer_email'] = $email;

        jsonResponse(200, [
            'success' => true,
            'redirect' => $redirect
        ]);
    }

    $stmt = $conn->prepare("
        SELECT id, full_name, email
        FROM customers
        WHERE email = ?
        LIMIT 1
    ");

    if (!$stmt) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Prepare failed (find by email): ' . $conn->error
        ]);
    }

    $stmt->bind_param('s', $email);

    if (!$stmt->execute()) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Execute failed (find by email): ' . $stmt->error
        ]);
    }

    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        $customerId = (int) $customer['id'];
        $authProvider = 'google';

        $stmt = $conn->prepare("
            UPDATE customers
            SET google_sub = ?, auth_provider = ?, avatar_url = ?, email_verified = ?, last_login_at = NOW()
            WHERE id = ?
        ");

        if (!$stmt) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Prepare failed (link google to existing email): ' . $conn->error
            ]);
        }

        $stmt->bind_param('sssii', $googleSub, $authProvider, $avatarUrl, $emailVerified, $customerId);

        if (!$stmt->execute()) {
            jsonResponse(500, [
                'success' => false,
                'message' => 'Execute failed (link google to existing email): ' . $stmt->error
            ]);
        }

        $_SESSION['customer_id'] = $customerId;
        $_SESSION['customer_name'] = $customer['full_name'];
        $_SESSION['customer_email'] = $email;

        jsonResponse(200, [
            'success' => true,
            'redirect' => $redirect
        ]);
    }

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

    if (!$stmt) {
        jsonResponse(500, [
            'success' => false,
            'message' => 'Prepare failed (insert new google user): ' . $conn->error
        ]);
    }

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
        jsonResponse(500, [
            'success' => false,
            'message' => 'Execute failed (insert new google user): ' . $stmt->error
        ]);
    }

    $customerId = (int) $conn->insert_id;

    $_SESSION['customer_id'] = $customerId;
    $_SESSION['customer_name'] = $fullName;
    $_SESSION['customer_email'] = $email;

    jsonResponse(200, [
        'success' => true,
        'redirect' => $redirect
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        'success' => false,
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}

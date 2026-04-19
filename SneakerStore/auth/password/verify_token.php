<?php
function findValidPasswordResetToken(mysqli $conn, string $rawToken): ?array //trả về array hoặc null
{
    // Token trống thì không cần kiểm tra DB
    if (trim($rawToken) === '') {
        return null;
    }

    // Băm token để so sánh với giá trị đã lưu trong DB
    $hashedToken = hash('sha256', $rawToken);

    $sql = "
        SELECT id, customer_id, email, expires_at, used_at
        FROM password_resets
        WHERE token_hash = ?
          AND used_at IS NULL
          AND expires_at >= NOW()
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $hashedToken);
    $stmt->execute();

    $resetRow = $stmt->get_result()->fetch_assoc();

    // Không tìm thấy token hợp lệ
    if (!$resetRow) {
        return null;
    }

    return $resetRow;
}

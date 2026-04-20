<?php
$message = $_SESSION['forgot_password_message'] ?? '';
$error = $_SESSION['forgot_password_error'] ?? '';
/* đặt lại dữ liệu gốc tránh hiển thị lại dữ liệu trước đó */
unset($_SESSION['forgot_password_message']);
unset($_SESSION['forgot_password_error']);
?>

<h1>Quên mật khẩu</h1>

<?php if ($message !== ''): ?>
    <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>


<form action="auth/password/request_reset.php" method="post">
    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    <button type="submit">Gửi link đặt lại mật khẩu</button>
</form>

<p>
    <a href="index.php?page=login">Quay lại đăng nhập</a>
</p>

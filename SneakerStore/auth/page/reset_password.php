<?php
$token = trim($_GET['token'] ?? '');
$message = $_SESSION['reset_password_message'] ?? '';
$error = $_SESSION['reset_password_error'] ?? '';

unset($_SESSION['reset_password_message']);
unset($_SESSION['reset_password_error']);
?>

<h1>Đặt lại mật khẩu</h1>

<?php if ($message !== ''): ?>
    <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($token === ''): ?>
    <p style="color: red;">Link đặt lại mật khẩu không đúng.</p>
    <p><a href="index.php?page=forgot_password">Đặt lại mật khẩu ?</a></p>
<?php else: ?>
    <form action="/auth/handle/perform_reset.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <p>
            <label>Mật khẩu mới</label><br>
            <input type="password" name="password" required>
        </p>

        <p>
            <label>Xác nhận mật khẩu mới</label><br>
            <input type="password" name="confirm_password" required>
        </p>

        <button type="submit">Cập nhật mật khẩu</button>
    </form>
<?php endif; ?>

<p>
    <a href="index.php?page=login">Quay lại trang đăng nhập</a>
</p>

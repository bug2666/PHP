<!-- nội dung email reset-->
<?php
function buildResetPasswordEmail(string $resetLink, string $email): string
{
    return '
    <h2>Đặt lại mật khẩu cho tài khoản SneakerStore</h2>
    <p>Xin chào,</p>
    
    <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản: <strong>' . htmlspecialchars($email) . '</strong></p>
    <p>Vui lòng bấm vào liên kết bên dưới để đặt lại mật khẩu:</p>
    <p><a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a></p>
    <p>Liên kết này sẽ hết hạn sau 30 phút.</p>
    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.</p>
';
}
?>
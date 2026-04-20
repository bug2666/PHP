<?php
require_once __DIR__ . '/../auth/google/config.php';


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($fullname === '' || $email === '' || $password === '' || $confirm_password === '' || $phone === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Địa chỉ email không hợp lệ';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu nhập lại không trùng khớp !';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải trên 6 kí tự !';
    } else {
        $sql = "select id from customers where email = ? limit 1";
        $sttm = $conn->prepare($sql); // phải có lệnh chuẩn bị trước thì các lệnh dưới mới được gợi ý và chạy
        $sttm->bind_param('s', $email);

        $sttm->execute();
        $result = $sttm->get_result(); // trả về false nếu sql truy vấn không ra kq

        if ($result->fetch_assoc()) {
            $error = "email đã tồn tại";
        } else {
            $hashedPassword = md5($password);
            $sql = "INSERT INTO customers (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            $stmt->bind_param("sssss", $fullname, $email, $hashedPassword, $phone, $address);

            if ($stmt->execute()) {
                $success = 'Đăng kí thành công. Bạn có thể đăng nhập.';
            } else {
                $error = 'Có lỗi xảy ra khi đăng kí.';
            }
        }
    }
}
?>
<h1>Đăng kí tài khoản</h1>

<?php if ($error !== ''): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="post" action="">
    <p>
        <label>Họ tên</label><br>
        <input type="text" name="full_name">
    </p>
    <p>
        <label>Email</label><br>
        <input type="email" name="email">
    </p>
    <p>
        <label>Mật khẩu</label><br>
        <input type="password" name="password">
    </p>
    <p>
        <label>Xác nhận mật khẩu</label><br>
        <input type="password" name="confirm_password">
    </p>
    <p>
        <label>Số điện thoại</label><br>
        <input type="text" name="phone">
    </p>
    <p>
        <label>Địa chỉ</label><br>
        <input type="text" name="address">
    </p>
    <button type="submit">Đăng kí</button>
</form>

<hr>
<h2>Đăng kí bằng google</h2>
<!-- thêm thư viện để đăng nhập được với google -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<!-- cấu hình để biết người dùng đang đăng nhập bằng google -->
 <div
    id="g_id_onload"
    data-client_id="<?php echo htmlspecialchars(GOOGLE_CLIENT_ID); ?>"
    data-callback="handleGoogleCredentialResponse"
    data-auto_prompt="false">
</div>
<!-- UI của nút -->
<div
    class="g_id_signin"
    data-type="standard"
    data-size="large"
    data-theme="outline"
    data-text="signin_with"
    data-shape="rectangular"
    data-logo_alignment="left">
</div>

<script>
async function handleGoogleCredentialResponse(response) {
    try {
        const res = await fetch('<?php echo APP_URL; ?>/auth/google/verify.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                credential: response.credential
            })
        });

        const data = await res.json();

        if (data.success) {
            window.location.href = data.redirect || 'index.php';
            return;
        }

        alert(data.message || 'Google login failed.');
    } catch (error) {
        alert('Cannot connect to Google login endpoint.');
    }
}
</script>
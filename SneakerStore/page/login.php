<?php
require_once __DIR__ . '/../auth/google/config.php';

if (isset($_SESSION['customer_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Vui lòng nhập Email và mật khẩu.';
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM customers WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();

        if ($customer && md5($password) === $customer['password']) {
            $_SESSION['customer_id'] = $customer['id']; // khởi tạo session để đánh dấu là đã đăng nhập thành cồng
            $_SESSION['customer_name'] = $customer['full_name'];
            $_SESSION['customer_email'] = $customer['email'];

            header('Location: index.php?page=cart');
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không đúng.';
        }
    }
}
?>

<h1>Đăng nhập</h1>

<?php if ($error !== ''): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="">
    <p>
        <label>Email</label><br>
        <input type="email" name="email">
    </p>
    <p>
        <label>Mật khẩu</label><br>
        <input type="password" name="password">
    </p>
    <button type="submit">Đăng nhập</button>
</form>

<hr>
<h2>Đăng nhập bằng google</h2>
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
        const res = await fetch('auth/google/verify.php', {
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

<?php
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
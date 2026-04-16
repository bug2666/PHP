<h1>Đăng nhập admin</h1>

<form method="post" action="">
    <p>
        <label>Username</label><br>
        <input type="text" name="username">
    </p>
    <p>
        <label>Password</label><br>
        <input type="password" name="password">
    </p>
    <button type="submit">Đăng nhập</button>
</form>

<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập thông tin.';
    } else {
        $sql = 'SELECT id, username, password FROM admins WHERE username = ? LIMIT 1';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && md5($password) === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Tài khoản hoặc mật khẩu không đúng.';
        }
    }
}
?>
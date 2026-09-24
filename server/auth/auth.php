<?php
session_start();
include __DIR__ . '/../../config/db.php';

$message = '';
$messageType = 'error';
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '../../public/index.html';
if (!preg_match('/^\.\.\/\.\.\/public\/index\.html(?:\?[A-Za-z0-9=&%_-]+)?$/', $redirect)) {
    $redirect = '../../public/index.html';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $message = 'Enter your name, a valid email, and a password of at least 6 characters.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $statement = mysqli_prepare($conn, 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'sss', $name, $email, $hash);
            }
            if ($statement && mysqli_stmt_execute($statement)) {
                session_regenerate_id(true);
                $_SESSION['user_name'] = $name;
                header('Location: ' . $redirect);
                exit;
            }
            $message = mysqli_errno($conn) === 1062 ? 'That email is already registered.' : 'Registration failed. Please try again.';
        }
    } elseif ($action === 'login') {
        $statement = mysqli_prepare($conn, 'SELECT name, password FROM users WHERE email = ?');
        mysqli_stmt_bind_param($statement, 's', $email);
        mysqli_stmt_execute($statement);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_name'] = $user['name'];
            header('Location: ' . $redirect);
            exit;
        }
        $message = 'Invalid email or password.';
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login or Register</title>
<link rel="stylesheet" href="../../public/assets/css/styles.css">
<style>
.auth-panel { max-width: 760px; margin: 70px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,.1); }
.auth-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
.auth-grid form { display: grid; gap: 12px; }
.auth-grid input { width: 100%; }
.auth-message { padding: 12px; margin-bottom: 20px; background: #fff3cd; }
@media (max-width: 650px) { .auth-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<main class="auth-panel">
<p><a href="<?php echo htmlspecialchars($redirect); ?>">Back to menu</a></p>
<h1>Customer account</h1>
<?php if ($message): ?><p class="auth-message"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
<div class="auth-grid">
<form method="post">
<h2>Login</h2>
<input type="hidden" name="action" value="login">
<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
<input type="email" name="email" placeholder="Email address" required>
<input type="password" name="password" placeholder="Password" required>
<button class="btn btn-primary" type="submit">Login</button>
</form>
<form method="post">
<h2>Register</h2>
<input type="hidden" name="action" value="register">
<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
<input type="text" name="name" placeholder="Full name" required>
<input type="email" name="email" placeholder="Email address" required>
<input type="password" name="password" placeholder="Password (6+ characters)" minlength="6" required>
<button class="btn btn-primary" type="submit">Create account</button>
</form>
</div>
</main>
</body>
</html>
<?php
require_once __DIR__ . '/session.php';

$message = '';
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '../../public/index.html';
if (!preg_match('/^\.\.\/\.\.\/public\/index\.html(?:\?[A-Za-z0-9=&%_-]+)?$/', $redirect)) {
    $redirect = '../../public/index.html';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($action === 'login') {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $password !== '') {
            session_regenerate_id(true);
            $_SESSION['user_name'] = $email;
            $_SESSION['user_email'] = $email;
            if (($_POST['remember_device'] ?? '') === '1') {
                $cookieOptions = [
                    'expires' => time() + 60 * 60 * 24 * 30,
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ];
                setcookie('nutricream_remember', '1', $cookieOptions);
                setcookie(session_name(), session_id(), $cookieOptions);
            } else {
                setcookie('nutricream_remember', '', [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
            header('Location: ' . $redirect);
            exit;
        }
        $message = 'Enter a valid email address and a password.';
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    $cookieOptions = [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    setcookie('nutricream_remember', '', $cookieOptions);
    setcookie(session_name(), '', $cookieOptions);
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign in</title>
<link rel="stylesheet" href="../../public/assets/css/styles.css">
<style>
.auth-panel { max-width: 440px; margin: 70px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,.1); }
.auth-form { display: grid; gap: 14px; }
.auth-form input[type="email"], .auth-form input[type="password"] { width: 100%; box-sizing: border-box; }
.remember-device { display: flex; align-items: center; gap: 9px; }
.remember-device input { width: auto; }
.auth-message { padding: 12px; margin-bottom: 20px; background: #fff3cd; }
</style>
</head>
<body>
<main class="auth-panel">
<p><a href="<?php echo htmlspecialchars($redirect); ?>">Back to menu</a></p>
<h1>Sign in</h1>
<p>Demo sign-in: any valid email address and non-empty password will continue.</p>
<?php if ($message): ?><p class="auth-message"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
<form method="post" class="auth-form">
<input type="hidden" name="action" value="login">
<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
<label for="email">Email address</label>
<input id="email" type="email" name="email" autocomplete="username" required>
<label for="password">Password</label>
<input id="password" type="password" name="password" autocomplete="current-password" required>
<label class="remember-device"><input type="checkbox" name="remember_device" value="1"> Remember this device</label>
<button class="btn btn-primary" type="submit">Login to account</button>
</form>
</main>
</body>
</html>
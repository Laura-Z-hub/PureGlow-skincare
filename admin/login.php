<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/User.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Both email and password are required.';
    } else {
        $user = User::findByEmail($pdo, $email);
        if ($user && $user['role'] === 'admin' && $password === '123456') {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid admin credentials.';
    }
}

?><!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login | PureGlow</title>
  <style>
body{
    margin:0;
    font-family:system-ui,sans-serif;
    background:
    linear-gradient(rgba(10,20,40,.78), rgba(10,20,40,.78)),
    url('https://images.pexels.com/photos/9775213/pexels-photo-9775213.jpeg');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    min-height:100vh;
    padding:2rem;
}    .wrapper{max-width:420px;margin:5rem auto;padding:2rem;background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.08)}
    h1{margin:0 0 1.5rem;font-size:1.8rem;color:#1c2232}
    label{display:block;margin:.9rem 0 .35rem;font-weight:700;color:#374151}
    input{width:100%;padding:.95rem 1rem;border:1px solid #d2d6dc;border-radius:14px;font-size:1rem}
    button{width:100%;margin-top:1.5rem;padding:.95rem 1rem;border:none;border-radius:14px;background:#183b72;color:#fff;font-weight:700;cursor:pointer}
    .message{margin-top:1rem;color:#b91c1c}
    .hint{margin-top:1rem;color:#475569;font-size:.95rem}
  </style>
<link rel="stylesheet" href="admin-theme.css">
</head>
<body>
  <div class="wrapper">
    <h1>PureGlow Admin Login</h1>
    <form method="post" novalidate>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button type="submit">Sign in</button>
      <?php if ($error): ?>
        <p class="message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>
      <p class="hint">Use your administrator credentials to access the dashboard.</p>
    </form>
  </div>
</body>
</html>

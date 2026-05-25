<?php
declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/models/EmailVerification.php';

$verified = EmailVerification::verify($pdo, trim($_GET['token'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="logo.png">
<link rel="shortcut icon" href="favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification | PureGlow</title>
<style>
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;font-family:system-ui,sans-serif;background:linear-gradient(rgba(10,20,40,.72),rgba(10,20,40,.72)),url('https://images.pexels.com/photos/6621461/pexels-photo-6621461.jpeg');background-size:cover;background-position:center;color:#2d1f16}
.card{width:min(440px,100%);background:rgba(255,255,255,.93);padding:2.3rem;border-radius:28px;box-shadow:0 20px 60px rgba(0,0,0,.35)}
h1{margin:0 0 1rem;font-size:2.3rem}
p{line-height:1.7;color:#6c5241}
a{display:inline-flex;margin-top:1rem;padding:.9rem 1.1rem;border-radius:14px;background:linear-gradient(135deg,#f3e4d2,#e8c9a7);color:#5b4332;text-decoration:none;font-weight:800}
</style>
</head>
<body>
<main class="card">
    <?php if ($verified): ?>
        <h1>Email verified</h1>
        <p>Your PureGlow account is ready. You can sign in now.</p>
    <?php else: ?>
        <h1>Verification failed</h1>
        <p>This verification link is invalid or was already used.</p>
    <?php endif; ?>
    <a href="signin.php">Go to sign in</a>
</main>
</body>
</html>

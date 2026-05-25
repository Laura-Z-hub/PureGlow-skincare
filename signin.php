<?php
declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
requireSession();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        if (($user['role'] ?? '') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }

        header('Location: index.html');
        exit;
    } else {
        $message = 'Invalid email or password.';
    }
}
?>

<!doctype html>
<html>
<head>
<link rel="icon" type="image/png" href="logo.png">
<link rel="shortcut icon" href="favicon.ico">
<meta charset="UTF-8">
<title>Sign In | PureGlow</title>

<style>

body{
    margin:0;
    font-family:system-ui,sans-serif;

    background:
    linear-gradient(rgba(10,20,40,.72), rgba(10,20,40,.72)),
    url('https://images.pexels.com/photos/6621461/pexels-photo-6621461.jpeg');

    background-size:cover;
    background-position:center;

    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;
}

.card{
    width:420px;

    background:rgba(255,255,255,.92);
    backdrop-filter:blur(16px);

    padding:2.5rem;

    border-radius:30px;

    box-shadow:
    0 20px 60px rgba(0,0,0,.35);
}

h1{
    margin-top:0;
    font-size:3rem;
    color:#2d1f16;
}

input{
    width:100%;
    padding:1rem;
    margin-bottom:1rem;

    border-radius:16px;
    border:1px solid #e7d6c3;

    background:#f8f1ea;

    font-size:1rem;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:1rem;

    border:none;
    border-radius:16px;

    font-size:1rem;
    font-weight:700;

    cursor:pointer;

    background:linear-gradient(135deg,#f3e4d2,#e8c9a7);

    color:#5b4332;

    box-shadow:
    0 0 10px rgba(255,217,120,.9),
    0 0 30px rgba(255,217,120,.55);

    transition:.25s;
}

button:hover{
    transform:translateY(-2px);

    box-shadow:
    0 0 20px rgba(255,217,120,1),
    0 0 40px rgba(255,217,120,.8);
}

.bottom-link{
    margin-top:1.4rem;
    text-align:center;
}

.bottom-link a{
    color:#8c6b52;
    text-decoration:none;
    font-weight:700;
}

.error{
    background:#ffe5e5;
    color:#b42318;

    padding:1rem;
    border-radius:14px;

    margin-bottom:1rem;
}

</style>
</head>

<body>

<div class="card">

    <h1>Sign In</h1>

    <?php if($message): ?>
        <div class="error">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <input
        type="email"
        name="email"
        placeholder="Email"
        required>

        <input
        type="password"
        name="password"
        placeholder="Password"
        required>

        <button type="submit">
            Sign In
        </button>

    </form>

    <div class="bottom-link">

        <a href="signup.php">
            Create new account
        </a>

    </div>

</div>

</body>
</html>

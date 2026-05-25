<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();


$_SESSION['seen_orders'] ??= 0;
$_SESSION['seen_contacts'] ??= 0;
$_SESSION['seen_bookings'] ??= 0;
$_SESSION['seen_subscriptions'] ??= 0;
$stats = [
    'products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'promotions' => (int) $pdo->query('SELECT COUNT(*) FROM promotions')->fetchColumn(),
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'contacts' => (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn(),
    'bookings' => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'subscriptions' => (int) $pdo->query('SELECT COUNT(*) FROM subscriptions')->fetchColumn(),
    'money' => (float) $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE status IN ('confirmed','completed')
")->fetchColumn(),
];
$notifications = [
    'orders' => $stats['orders'] - $_SESSION['seen_orders'],
    'contacts' => $stats['contacts'] - $_SESSION['seen_contacts'],
    'bookings' => $stats['bookings'] - $_SESSION['seen_bookings'],
    'subscriptions' => $stats['subscriptions'] - $_SESSION['seen_subscriptions'],
];
$links = [
    'products' => 'products.php',
    'promotions' => 'promotions.php',
    'orders' => 'orders.php',
    'customers' => 'customer.php',
    'contacts' => 'contacts.php',
    'bookings' => 'bookings.php',
    'subscriptions' => 'subscriptions.php',
    'money' => 'money.php',
];
?>
<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<title>Admin Dashboard | PureGlow</title>

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
}

header{
    padding:1.8rem 2rem;
    background:#183b72;
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

header h1{
    margin:0;
    font-size:1.7rem;
}

.logout-btn{
    background:#7f1d1d;
    color:#fff;
    text-decoration:none;
    padding:.9rem 1.2rem;
    border-radius:14px;
    font-weight:700;
}

.container{
    max-width:1120px;
    margin:2rem auto;
    padding:0 1rem;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:1rem;
}

.card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    padding:1.4rem;
    border-radius:24px;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
}

.card h2{
    margin:0 0 .8rem;
    font-size:1.1rem;
    color:#475569;
}

.card p{
    margin:0;
    font-size:2.6rem;
    font-weight:700;
    color:#0f172a;
}

.manage-btn{
    display:inline-block;
    margin-top:1rem;
    background:#183b72;
    color:#fff;
    padding:1rem 1.4rem;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
    font-size:.95rem;
    box-shadow:0 8px 20px rgba(24,59,114,.35);
}
.card{
    position:relative;
}

.notify-badge{
    position:absolute;
    top:18px;
    right:18px;
    min-width:34px;
    height:34px;
    padding:0 .45rem;
    border-radius:999px;
    background:#ff2bd6;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:.95rem;

    box-shadow:
    0 0 12px #ff2bd6,
    0 0 24px #ff2bd6,
    0 0 40px rgba(255,43,214,.9);
}

</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body data-live-counts="<?= htmlspecialchars(json_encode($stats), ENT_QUOTES, 'UTF-8') ?>">

<header>
    <h1>PureGlow Admin Dashboard</h1>
    <a class="logout-btn" href="logout.php">Logout</a>
</header>

<main class="container">

<div class="grid">

<?php foreach ($stats as $label => $value): ?>

    <div class="card" data-live-card="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
    <?php if (
    isset($notifications[$label]) &&
    $notifications[$label] > 0
): ?>
    <span class="notify-badge" data-live-badge="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
        <?= $notifications[$label] ?>
    </span>
<?php endif; ?>


        <h2><?= htmlspecialchars(ucwords($label)) ?></h2>

        <p data-live-value="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($label === 'money'): ?>
        €<?= number_format((float)$value, 2) ?>
    <?php else: ?>
        <?= $value ?>
    <?php endif; ?>
</p>

        <?php if (isset($links[$label])): ?>
            <a class="manage-btn" href="<?= htmlspecialchars($links[$label]) ?>">
                Manage
            </a>
        <?php endif; ?>

    </div>

<?php endforeach; ?>

</div>

</main>

<script src="admin-live.js"></script>
</body>
</html>

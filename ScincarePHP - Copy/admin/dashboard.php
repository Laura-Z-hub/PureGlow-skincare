<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$stats = [
    'products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'customers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'contacts' => (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn(),
    'bookings' => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'subscriptions' => (int) $pdo->query('SELECT COUNT(*) FROM subscriptions')->fetchColumn(),
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
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
    header{padding:1.8rem 2rem;background:#183b72;color:#fff}
    header h1{margin:0;font-size:1.7rem}
    .container{max-width:1120px;margin:2rem auto;padding:0 1rem}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}
    .card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    padding:1.4rem;
    border-radius:24px;
    box-shadow:0 10px 30px rgba(0,0,0,.18);
}   .card h2{margin:0 0 .8rem;font-size:1.1rem;color:#475569}
    .card p{margin:0;font-size:2.6rem;font-weight:700;color:#0f172a}
    .nav{margin:1.4rem 0;display:flex;gap:.85rem;flex-wrap:wrap}
    .nav a{display:inline-flex;padding:.8rem 1.1rem;border-radius:14px;background:#fff;color:#183b72;text-decoration:none;border:1px solid rgba(24,59,114,.16)}
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
    transition:.25s;
}

.manage-btn:hover{
    transform:translateY(-3px);
    background:#24519b;
}
  </style>
</head>
<body>
  <header>
    <h1>PureGlow Admin Dashboard</h1>
  </header>
  <main class="container">
    
    <?php
$links = [
    'products' => 'products.php',
    'orders' => 'orders.php',
    'customers' => 'customers.php',
    'contacts' => 'contacts.php',
    'bookings' => 'bookings.php',
    'subscriptions' => 'subscriptions.php',
];
?>
<div class="grid">

<?php foreach ($stats as $label => $value): ?>

    <div class="card">

        <h2><?php echo htmlspecialchars(ucwords($label)); ?></h2>

        <p><?php echo $value; ?></p>

        <?php if($label === 'products'): ?>
            <a class="manage-btn" href="products.php">
                Manage
            </a>
        <?php endif; ?>

        <?php if($label === 'bookings'): ?>
            <a class="manage-btn" href="bookings.php">
                Manage
            </a>
        <?php endif; ?>

        <?php if($label === 'contacts'): ?>
            <a class="manage-btn" href="contacts.php">
                Manage
            </a>
        <?php endif; ?>

    </div>

<?php endforeach; ?>

</div>
  </main>
</body>
</html>

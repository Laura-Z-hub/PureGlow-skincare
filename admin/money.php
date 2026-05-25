<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$total = (float)$pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE status IN ('confirmed','completed')
")->fetchColumn();

$orders = $pdo->query("
    SELECT *
    FROM orders
    WHERE status IN ('confirmed','completed')
    ORDER BY id DESC
")->fetchAll();
?>

<!doctype html>
<html>
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<title>Money | Admin</title>
<style>
body{
    margin:0;
    font-family:system-ui,sans-serif;
    background:
    linear-gradient(rgba(10,20,40,.82), rgba(10,20,40,.82)),
    url('https://images.pexels.com/photos/4386431/pexels-photo-4386431.jpeg');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    min-height:100vh;
    padding:2rem;
    color:#fff;
}

.back-btn{
    display:inline-block;
    background:#183b72;
    color:#fff;
    text-decoration:none;
    padding:1rem 1.4rem;
    border-radius:16px;
    font-weight:800;
    box-shadow:0 10px 25px rgba(24,59,114,.35);
    margin-bottom:2rem;
}

.card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    padding:2.2rem;
    border-radius:28px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
    margin-bottom:2rem;
}

.card h1{
    color:#111827;
    margin:0 0 1rem;
}

.total{
    font-size:3.8rem;
    color:#16a34a;
    font-weight:900;
    text-shadow:0 0 18px rgba(22,163,74,.35);
}

.table-card{
    background:rgba(255,255,255,.94);
    backdrop-filter:blur(10px);
    border-radius:28px;
    overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#183b72;
}

th{
    color:#fff;
    text-align:left;
    padding:1.2rem;
}

td{
    padding:1.2rem;
    color:#111827;
    background:#fff;
    border-bottom:1px solid #e5e7eb;
    font-weight:600;
}

tr:hover td{
    background:#f8fafc;
}

.status{
    display:inline-block;
    padding:.45rem .8rem;
    border-radius:999px;
    background:#dcfce7;
    color:#166534;
    font-weight:800;
}

.payment{
    color:#183b72;
    font-weight:800;
}
</style>
<link rel="stylesheet" href="admin-theme.css">
</head>
<body>

<a class="back-btn" href="dashboard.php">← Back Dashboard</a>

<div class="card">
<h1>Total Money</h1>
<div class="total">€<?= number_format($total, 2) ?></div>
</div>

<table>
<tr>
<th>ID</th>
<th>User ID</th>
<th>Status</th>
<th>Total</th>
<th>Payment</th>
<th>Date</th>
</tr>

<?php foreach($orders as $order): ?>
<tr>
<td><?= $order['id'] ?></td>
<td><?= $order['user_id'] ?></td>
<td><?= htmlspecialchars($order['status']) ?></td>
<td>€<?= number_format((float)$order['total_amount'], 2) ?></td>
<td><?= htmlspecialchars($order['payment_method']) ?></td>
<td><?= htmlspecialchars($order['created_at']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
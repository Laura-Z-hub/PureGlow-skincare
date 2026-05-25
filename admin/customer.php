<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();

$customers = $pdo->query("
    SELECT id, name, email, role, created_at
    FROM users
    WHERE role = 'customer'
    ORDER BY id DESC
")->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<title>Customers</title>

<style>

body{
    margin:0;
    font-family:system-ui,sans-serif;

    background:
    linear-gradient(rgba(10,20,40,.82), rgba(10,20,40,.82)),
    url('https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg');

    background-size:cover;
    background-position:center;
    background-attachment:fixed;

    min-height:100vh;
    padding:2rem;
    color:#fff;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:2rem;
}

.topbar h1{
    margin:0;
    font-size:2.3rem;
}

.back-btn{
    background:#183b72;
    color:#fff;
    text-decoration:none;
    padding:1rem 1.4rem;
    border-radius:16px;
    font-weight:700;
    box-shadow:0 10px 25px rgba(24,59,114,.35);
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
    border-bottom:1px solid #e5e7eb;
    background:#fff;
}

tr:hover td{
    background:#f8fafc;
}

.role{
    display:inline-block;
    padding:.45rem .9rem;
    border-radius:999px;
    background:#ff2bd6;
    color:#fff;
    font-weight:700;

    box-shadow:
    0 0 10px #ff2bd6,
    0 0 20px rgba(255,43,214,.8);
}

.empty{
    padding:2rem;
    color:#fff;
    font-size:1.1rem;
}

</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body>

<div class="topbar">

    <h1>Customers</h1>

    <a class="back-btn" href="dashboard.php">
        Back Dashboard
    </a>

</div>

<?php if(empty($customers)): ?>

    <div class="empty">
        No customers found.
    </div>

<?php else: ?>

<div class="table-card">

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Created</th>
</tr>
</thead>

<tbody>

<?php foreach($customers as $customer): ?>

<tr>

<td>
    #<?= $customer['id'] ?>
</td>

<td>
    <?= htmlspecialchars($customer['name'] ?? '') ?>
</td>

<td>
    <?= htmlspecialchars($customer['email'] ?? '') ?>
</td>

<td>
    <span class="role">
        <?= htmlspecialchars($customer['role'] ?? '') ?>
    </span>
</td>

<td>
    <?= htmlspecialchars($customer['created_at'] ?? '') ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</body>
</html>
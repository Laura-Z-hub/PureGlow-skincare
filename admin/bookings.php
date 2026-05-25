<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();
$_SESSION['seen_bookings'] =
(int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$bookings = $pdo->query("
    SELECT * FROM bookings
    ORDER BY id DESC
")->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bookings | Admin</title>

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
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:2rem;
}

.buttons{
    display:flex;
    gap:10px;
}

.button{
    background:#183b72;
    color:#fff;
    padding:.8rem 1rem;
    border-radius:12px;
    text-decoration:none;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
}

th,td{
    padding:1rem;
    border-bottom:1px solid #eee;
    text-align:left;
}

th{
    background:#183b72;
    color:#fff;
}

tr:hover{
    background:#f8fafc;
}

</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body
    data-live-key="bookings"
    data-live-counts="<?= htmlspecialchars(json_encode(['bookings' => count($bookings)]), ENT_QUOTES, 'UTF-8') ?>"
>

<div class="topbar">

    <h1>Bookings</h1>

    <div class="buttons">

        <a class="button" href="dashboard.php">
            Dashboard
        </a>

    </div>

</div>

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Skin Type</th>
    <th>Date</th>
    <th>Time</th>
    <th>Created</th>
</tr>
</thead>

<tbody>

<?php foreach($bookings as $booking): ?>

<tr>

<td><?= $booking['id'] ?></td>

<td><?= htmlspecialchars($booking['name']) ?></td>

<td><?= htmlspecialchars($booking['phone']) ?></td>

<td><?= htmlspecialchars($booking['email']) ?></td>

<td><?= htmlspecialchars($booking['skin_type']) ?></td>

<td><?= htmlspecialchars($booking['appointment_date']) ?></td>
<td><?= htmlspecialchars($booking['appointment_time']) ?></td>

<td><?= htmlspecialchars($booking['created_at']) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<script src="admin-live.js"></script>
</body>
</html>

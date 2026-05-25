<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();
$invoiceStatus = $_GET['invoice'] ?? '';
$_SESSION['seen_orders'] =
(int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$orders = $pdo->query("
    SELECT *
    FROM orders
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Orders | PureGlow</title>

<style>

body{
    margin:0;
    font-family:system-ui,sans-serif;

    background:
    linear-gradient(rgba(10,20,40,.82), rgba(10,20,40,.82)),
    url('https://images.pexels.com/photos/5980597/pexels-photo-5980597.jpeg');

    background-size:cover;
    background-position:center;
    background-attachment:fixed;

    min-height:100vh;
    color:#fff;
}

.wrapper{
    max-width:1200px;
    margin:auto;
    padding:2rem;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:2rem;
}

.topbar h1{
    margin:0;
    font-size:2rem;
}

.back-btn{
    background:#183b72;
    color:#fff;
    text-decoration:none;
    padding:.9rem 1.3rem;
    border-radius:14px;
    font-weight:700;
}

.table-card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    border-radius:24px;
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
    font-size:.95rem;
    letter-spacing:.5px;
}

td{
    padding:1.1rem 1.2rem;
    color:#111827;
    border-bottom:1px solid #e5e7eb;
    font-weight:500;
}

tr:hover{
    background:#f8fafc;
}

.status{
    display:inline-block;
    padding:.45rem .8rem;
    border-radius:999px;
    font-size:.82rem;
    font-weight:700;
    background:#dcfce7;
    color:#166534;
}

.empty{
    padding:2rem;
    text-align:center;
    color:#374151;
}
.status-btn{
    border:none;
    padding:.7rem 1rem;
    border-radius:12px;
    font-weight:800;
    cursor:pointer;
    color:#fff;
    transition:.25s;
}

.confirm-btn{
    background:#39ff14;
    color:#041304;

    box-shadow:
    0 0 12px #39ff14,
    0 0 25px rgba(57,255,20,.7);
}

.waiting-btn{
    background:#ff3b82;
    color:#fff;

    box-shadow:
    0 0 12px #ff3b82,
    0 0 25px rgba(255,59,130,.7);
}
.notice{
    margin-bottom:1rem;
    padding:1rem 1.2rem;
    border-radius:14px;
    font-weight:800;
}

.notice.success{
    background:#dcfce7;
    color:#166534;
}

.notice.error{
    background:#fee2e2;
    color:#991b1b;
}

</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body
    data-live-key="orders"
    data-live-counts="<?= htmlspecialchars(json_encode(['orders' => count($orders)]), ENT_QUOTES, 'UTF-8') ?>"
>

<div class="wrapper">

    <div class="topbar">
        <h1>Orders</h1>

        <a class="back-btn" href="dashboard.php">
            Back Dashboard
        </a>
    </div>

    <?php if($invoiceStatus === 'sent'): ?>
        <div class="notice success">
            Invoice email was sent to the customer.
        </div>
    <?php elseif($invoiceStatus === 'failed'): ?>
        <div class="notice error">
            Order was confirmed, but the invoice email could not be sent. Check XAMPP mail settings.
        </div>
    <?php endif; ?>

    <div class="table-card">

        <?php if(empty($orders)): ?>

            <div class="empty">
                No orders found.
            </div>

        <?php else: ?>

            <table>

<thead>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Total</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Date</th>
    </tr>
</thead>

<tbody>

<?php foreach($orders as $order): ?>

    <tr>

        <td>
            #<?= $order['id'] ?>
        </td>

        <td>
            User <?= $order['user_id'] ?>
        </td>

        <td>
            €<?= number_format((float)$order['total_amount'], 2) ?>
        </td>

        <td>

            <form method="post" action="update-order-status.php">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $order['id'] ?>"
                >

                <?php if($order['status'] === 'confirmed'): ?>

                    <button
                        class="status-btn confirm-btn"
                        type="submit"
                        name="status"
                        value="waiting"
                    >
                        Confirmed
                    </button>

                <?php else: ?>

                    <button
                        class="status-btn waiting-btn"
                        type="submit"
                        name="status"
                        value="confirmed"
                    >
                        Waiting
                    </button>

                <?php endif; ?>

            </form>

        </td>

        <td>
            <?= htmlspecialchars($order['payment_method']) ?>
        </td>

        <td>
            <?= htmlspecialchars($order['created_at']) ?>
        </td>

    </tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

</div>

<script src="admin-live.js"></script>
</body>
</html>

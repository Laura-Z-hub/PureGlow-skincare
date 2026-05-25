<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();
$_SESSION['seen_subscriptions'] =
(int)$pdo->query("SELECT COUNT(*) FROM subscriptions")->fetchColumn();
$subscriptions = $pdo->query("
    SELECT *
    FROM subscriptions
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

<title>Subscriptions | PureGlow</title>

<style>

body{
    margin:0;
    font-family:system-ui,sans-serif;

    background:
    linear-gradient(rgba(10,20,40,.82), rgba(10,20,40,.82)),
    url('https://images.pexels.com/photos/7430701/pexels-photo-7430701.jpeg');

    background-size:cover;
    background-position:center;
    background-attachment:fixed;

    min-height:100vh;
    color:#fff;
}

.wrapper{
    max-width:1100px;
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
    box-shadow:0 10px 25px rgba(24,59,114,.35);
    transition:.25s;
}

.back-btn:hover{
    background:#24519b;
    transform:translateY(-2px);
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
    padding:1.1rem;
    font-size:.95rem;
}

td{
    padding:1rem 1.1rem;
    color:#111827;
    border-bottom:1px solid #e5e7eb;
    background:#fff;
}

tr:hover td{
    background:#f8fafc;
}

.empty{
    padding:2rem;
    text-align:center;
    color:#374151;
    background:#fff;
}

.email-badge{
    display:inline-block;
    background:#dbeafe;
    color:#1d4ed8;
    padding:.45rem .8rem;
    border-radius:999px;
    font-size:.85rem;
    font-weight:700;
}

</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body
    data-live-key="subscriptions"
    data-live-counts="<?= htmlspecialchars(json_encode(['subscriptions' => count($subscriptions)]), ENT_QUOTES, 'UTF-8') ?>"
>

<div class="wrapper">

    <div class="topbar">

        <h1>Subscriptions</h1>

        <a class="back-btn" href="dashboard.php">
            Back Dashboard
        </a>

    </div>

    <div class="table-card">

        <?php if(empty($subscriptions)): ?>

            <div class="empty">
                No subscriptions found.
            </div>

        <?php else: ?>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach($subscriptions as $subscription): ?>

                <tr>

                    <td>
                        #<?= $subscription['id'] ?>
                    </td>

                    <td>
                        <span class="email-badge">
                            <?= htmlspecialchars($subscription['email']) ?>
                        </span>
                    </td>

                    <td>
                        <?= htmlspecialchars($subscription['subscribed_at']) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php endif; ?>

    </div>

</div>

<script src="admin-live.js"></script>
</body>
</html>

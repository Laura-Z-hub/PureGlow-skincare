<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();
$_SESSION['seen_contacts'] =
(int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$contacts = $pdo->query("
    SELECT *
    FROM contacts
    ORDER BY id DESC
")->fetchAll();

?>

<!doctype html>
<html>
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<title>Contacts</title>

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

a.button{
    background:#183b72;
    color:#fff;
    padding:1rem 1.2rem;
    border-radius:14px;
    text-decoration:none;
}

table{
    width:100%;
    background:#fff;
    border-collapse:collapse;
    border-radius:24px;
    overflow:hidden;
}

th{
    background:#183b72;
    color:#fff;
    padding:1rem;
    text-align:left;
}

td{
    padding:1rem;
    border-bottom:1px solid #eee;
    vertical-align:top;
}

.message{
    max-width:400px;
    line-height:1.6;
    color:#475569;
}

</style>
<link rel="stylesheet" href="admin-theme.css">

</head>

<body
    data-live-key="contacts"
    data-live-counts="<?= htmlspecialchars(json_encode(['contacts' => count($contacts)]), ENT_QUOTES, 'UTF-8') ?>"
>

<div class="topbar">
    <h1>Contacts</h1>

    <a class="button" href="dashboard.php">
        Dashboard
    </a>
</div>

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Created</th>
</tr>
</thead>

<tbody>

<?php foreach($contacts as $contact): ?>

<tr>

<td><?= $contact['id'] ?></td>

<td>
<?= htmlspecialchars($contact['name'] ?? '') ?>
</td>

<td>
<?= htmlspecialchars($contact['email'] ?? '') ?>
</td>

<td class="message">
<?= nl2br(htmlspecialchars($contact['message'] ?? '')) ?>
</td>

<td>
<?= htmlspecialchars($contact['created_at'] ?? '') ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<script src="admin-live.js"></script>
</body>
</html>

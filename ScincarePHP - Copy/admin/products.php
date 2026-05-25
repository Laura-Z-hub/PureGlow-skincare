<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
requireAdmin();

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Products | Admin</title>

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
    padding:.8rem 1rem;
    border-radius:12px;
    text-decoration:none;
}

table{
    width:100%;
    background:#fff;
    border-collapse:collapse;
    border-radius:20px;
    overflow:hidden;
}

th,td{
    padding:1rem;
    border-bottom:1px solid #eee;
    text-align:left;
}

img{
    width:70px;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="topbar">

    <h1>Products</h1>

    <div style="display:flex;gap:10px;">

        <a class="button" href="dashboard.php">
            Dashboard
        </a>

        <a class="button" href="product-create.php">
            Add Product
        </a>

    </div>

</div>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Brand</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach($products as $product): ?>

<tr>

<td><?= $product['id'] ?></td>

<td>
<img src="<?= json_decode($product['images'])[0] ?>" alt="">
</td>

<td><?= htmlspecialchars($product['name']) ?></td>

<td><?= htmlspecialchars($product['brand']) ?></td>

<td>€<?= $product['price'] ?></td>

<td><?= $product['stock'] ?></td>

<td>
<a href="product-edit.php?id=<?= $product['id'] ?>">Edit</a>
|
<a href="product-delete.php?id=<?= $product['id'] ?>"
onclick="return confirm('Delete product?')">
Delete
</a>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</body>
</html>
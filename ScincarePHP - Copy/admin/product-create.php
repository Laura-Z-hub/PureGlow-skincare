<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $sku = $_POST['sku'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare("
        INSERT INTO products
        (name, slug, sku, category, brand, description, price, stock, images)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $slug,
        $sku,
        $category,
        $brand,
        $description,
        $price,
        $stock,
        json_encode([$image])
    ]);

    header('Location: products.php');
    exit;
}

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Product</title>

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

form{
    background:#fff;
    max-width:700px;
    margin:auto;
    padding:2rem;
    border-radius:20px;
}

input, textarea, select{
    width:100%;
    padding:1rem;
    margin-bottom:1rem;
    box-sizing:border-box;
}

textarea{
    min-height:120px;
}

button, a.button{
    background:#183b72;
    color:#fff;
    border:none;
    padding:1rem;
    border-radius:12px;
    text-decoration:none;
    display:inline-block;
    cursor:pointer;
}

.actions{
    display:flex;
    gap:10px;
}
</style>

</head>

<body>

<form method="post">

<h1>Add Product</h1>

<input name="name" placeholder="Name" required>

<input name="slug" placeholder="Slug" required>

<input name="sku" placeholder="SKU" required>

<select name="category" required>
    <option value="">Select category</option>
    <option value="cleanser">Cleanser</option>
    <option value="serum">Serum</option>
    <option value="moisturizer">Moisturizer</option>
    <option value="mask">Mask</option>
    <option value="spf">SPF</option>
    <option value="toner">Toner</option>
    <option value="essence">Essence</option>
</select>

<input name="brand" placeholder="Brand" required>

<textarea name="description" placeholder="Description"></textarea>

<input name="price" placeholder="Price" type="number" step="0.01">

<input name="stock" placeholder="Stock" type="number">

<input name="image" placeholder="Image URL">

<div class="actions">
    <button type="submit">Add Product</button>
    <a class="button" href="products.php">Back</a>
</div>

</form>

</body>
</html>
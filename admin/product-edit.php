<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/product-image-upload.php';

requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die('Product not found');
}

$images = json_decode($product['images'] ?? '[]', true);
$image = $images[0] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newImages = collectProductImages($_FILES, $_POST['image'] ?? '', $images);

        if (empty($newImages)) {
            throw new RuntimeException('Please add an image URL or upload an image.');
        }

        $stmt = $pdo->prepare("
            UPDATE products
            SET
                name = ?,
                slug = ?,
                sku = ?,
                category = ?,
                brand = ?,
                description = ?,
                price = ?,
                currency = ?,
                stock = ?,
                images = ?,
                featured = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['slug'],
            $_POST['sku'],
            $_POST['category'],
            $_POST['brand'],
            $_POST['description'],
            $_POST['price'],
            $_POST['currency'],
            $_POST['stock'],
            json_encode($newImages),
            isset($_POST['featured']) ? 1 : 0,
            $id
        ]);

        header('Location: products.php');
        exit;
    } catch (RuntimeException $error) {
        $message = $error->getMessage();
    }
}
?>

<!doctype html>
<html>
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<title>Edit Product</title>

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
    max-width:800px;
    margin:auto;
    padding:2rem;
    border-radius:20px;
}

label{
    display:block;
    font-weight:700;
    margin:.8rem 0 .3rem;
}

input, textarea, select{
    width:100%;
    padding:1rem;
    margin-bottom:1rem;
    box-sizing:border-box;
}

textarea{
    min-height:140px;
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

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:1rem;
    border-radius:12px;
    margin-bottom:1rem;
    font-weight:700;
}

.preview{
    width:120px;
    height:120px;
    object-fit:cover;
    border-radius:14px;
    margin-bottom:1rem;
}
</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body>

<form method="post" enctype="multipart/form-data">

<h1>Edit Product</h1>

<?php if($message): ?>
    <div class="error"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<label>Name</label>
<input name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

<label>Slug</label>
<input name="slug" value="<?= htmlspecialchars($product['slug']) ?>" required>

<label>SKU</label>
<input name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required>

<label>Category</label>

<select name="category" required>
    <option value="cleanser" <?= $product['category'] === 'cleanser' ? 'selected' : '' ?>>Cleanser</option>
    <option value="serum" <?= $product['category'] === 'serum' ? 'selected' : '' ?>>Serum</option>
    <option value="moisturizer" <?= $product['category'] === 'moisturizer' ? 'selected' : '' ?>>Moisturizer</option>
    <option value="mask" <?= $product['category'] === 'mask' ? 'selected' : '' ?>>Mask</option>
    <option value="spf" <?= $product['category'] === 'spf' ? 'selected' : '' ?>>SPF</option>
    <option value="toner" <?= $product['category'] === 'toner' ? 'selected' : '' ?>>Toner</option>
    <option value="essence" <?= $product['category'] === 'essence' ? 'selected' : '' ?>>Essence</option>
</select>

<label>Brand</label>
<input name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required>

<label>Description</label>
<textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

<label>Price</label>
<input name="price" type="number" step="0.01" value="<?= htmlspecialchars((string)$product['price']) ?>">

<label>Currency</label>
<input name="currency" value="<?= htmlspecialchars($product['currency']) ?>">

<label>Stock</label>
<input name="stock" type="number" value="<?= htmlspecialchars((string)$product['stock']) ?>">

<label>Image URL</label>
<input name="image" value="<?= htmlspecialchars($image) ?>">

<?php if($image): ?>
    <img class="preview" src="<?= htmlspecialchars(productImageAdminSrc($image)) ?>" alt="">
<?php endif; ?>

<label>Upload image from phone or computer</label>
<input name="product_images[]" type="file" accept="image/*" multiple>

<label>
    <input type="checkbox" name="featured" value="1" <?= $product['featured'] ? 'checked' : '' ?>>
    Featured product
</label>

<div class="actions">
    <button type="submit">Save Changes</button>
    <a class="button" href="products.php">Back</a>
</div>

</form>

</body>
</html>

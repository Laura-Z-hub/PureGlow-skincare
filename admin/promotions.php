<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $discountPercent = isset($_POST['discount_percent']) ? (int) $_POST['discount_percent'] : 0;

    if ($productId > 0 && $discountPercent > 0) {
        $exists = $pdo->prepare("
            SELECT id
            FROM promotions
            WHERE product_id = ?
              AND active = 1
            LIMIT 1
        ");
        $exists->execute([$productId]);

        if ($exists->fetch()) {
            header('Location: promotions.php');
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO promotions
            (product_id, discount_percent)
            VALUES
            (?, ?)
        ");

        $stmt->execute([
            $productId,
            $discountPercent
        ]);
    }

    header('Location: promotions.php');
    exit;
}

$products = $pdo->query("
    SELECT products.*
    FROM products
    LEFT JOIN promotions
        ON promotions.product_id = products.id
        AND promotions.active = 1
    WHERE promotions.id IS NULL
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$promotions = $pdo->query("
    SELECT promotions.*, products.name
    FROM promotions
    JOIN products ON products.id = promotions.product_id
    ORDER BY promotions.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="../logo.png">
<link rel="shortcut icon" href="../favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Promotions | PureGlow</title>

<style>
body{
    margin:0;
    font-family:system-ui,sans-serif;
    background:
    linear-gradient(rgba(10,20,40,.82), rgba(10,20,40,.82)),
    url('https://images.pexels.com/photos/5622934/pexels-photo-5622934.jpeg');
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
    font-size:2.2rem;
}

.back-btn{
    background:#183b72;
    color:#fff;
    text-decoration:none;
    padding:1rem 1.4rem;
    border-radius:16px;
    font-weight:700;
    box-shadow:0 10px 25px rgba(24,59,114,.35);
    transition:.25s;
}

.back-btn:hover{
    background:#24519b;
    transform:translateY(-2px);
}

.form-card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    padding:2rem;
    border-radius:28px;
    box-shadow:0 20px 60px rgba(0,0,0,.35);
    margin-bottom:2rem;
}

.form-card form{
    display:flex;
    flex-direction:column;
    gap:1rem;
}

.product-search{
    width:100%;
    box-sizing:border-box;
    padding:1rem;
    border-radius:14px;
    border:1px solid #dbe2ea;
    font-size:1rem;
}

select,
input{
    width:100%;
    box-sizing:border-box;
    padding:1rem;
    border-radius:14px;
    border:1px solid #dbe2ea;
    font-size:1rem;
}

button{
    width:220px;
    padding:1rem;
    border:none;
    border-radius:14px;
    background:#183b72;
    color:#fff;
    font-weight:700;
    cursor:pointer;
    transition:.25s;
}

button:hover{
    background:#24519b;
    transform:translateY(-2px);
}

.table-card{
    background:rgba(255,255,255,.92);
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
    font-size:1rem;
}

td{
    padding:1.2rem;
    color:#111827;
    background:#fff;
    border-bottom:1px solid #e5e7eb;
}

tr:hover td{
    background:#f8fafc;
}

.discount{
    color:#ef4444;
    font-weight:800;
    font-size:1.1rem;
}

.delete-btn{
    display:inline-block;
    background:#dc2626;
    color:#fff;
    text-decoration:none;
    padding:.7rem 1rem;
    border-radius:12px;
    font-weight:700;
    transition:.25s;
}

.delete-btn:hover{
    background:#b91c1c;
    transform:translateY(-2px);
}

.empty{
    padding:2rem;
    text-align:center;
    color:#374151;
    background:#fff;
}
</style>
<link rel="stylesheet" href="admin-theme.css">
</head>

<body>

<div class="wrapper">

    <div class="topbar">
        <h1>Promotions</h1>

        <a class="back-btn" href="dashboard.php">
            Back Dashboard
        </a>
    </div>

    <div class="form-card">

        <form method="post">

            <input
                class="product-search"
                id="promotionProductSearch"
                type="search"
                placeholder="Search product..."
                autocomplete="off"
            >

            <select name="product_id" id="promotionProductSelect" required>
                <option value="">Select product</option>

                <?php foreach($products as $product): ?>
                    <option value="<?= (int)$product['id'] ?>">
                        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input
                type="number"
                name="discount_percent"
                placeholder="Discount %"
                min="1"
                max="100"
                required
            >

            <button type="submit">
                Add Promotion
            </button>

        </form>

    </div>

    <div class="table-card">

        <?php if(empty($promotions)): ?>

            <div class="empty">
                No promotions found.
            </div>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Discount</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach($promotions as $promotion): ?>

                        <tr>
                            <td>#<?= (int)$promotion['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($promotion['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <span class="discount">
                                    -<?= (int)$promotion['discount_percent'] ?>%
                                </span>
                            </td>

                            <td>
                                <a
                                    class="delete-btn"
                                    href="promotions-delete.php?id=<?= (int)$promotion['id'] ?>"
                                    onclick="return confirm('Remove this product from promotions?');"
                                >
                                    Remove
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php endif; ?>

    </div>

</div>

<script>
const promotionProductSearch = document.getElementById("promotionProductSearch");
const promotionProductSelect = document.getElementById("promotionProductSelect");

if (promotionProductSearch && promotionProductSelect) {
    const productOptions = Array.from(promotionProductSelect.options).map(option => ({
        value: option.value,
        text: option.text,
    }));

    promotionProductSearch.addEventListener("input", () => {
        const query = promotionProductSearch.value.trim().toLowerCase();
        const selectedValue = promotionProductSelect.value;

        promotionProductSelect.innerHTML = "";

        productOptions
            .filter(option => option.value === "" || option.text.toLowerCase().includes(query))
            .forEach(option => {
                const item = document.createElement("option");
                item.value = option.value;
                item.textContent = option.text;
                item.selected = option.value === selectedValue;
                promotionProductSelect.appendChild(item);
            });
    });
}
</script>

</body>
</html>

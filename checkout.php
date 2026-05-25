<?php
declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/models/Cart.php';
require_once __DIR__ . '/models/Product.php';

$user = requireCustomer();
$cartItems = Cart::getByUser($pdo, (int) $user['id']);
$items = [];
$total = 0.0;

foreach ($cartItems as $item) {
    $product = Product::findById($pdo, (int) ($item['product_id'] ?? 0));

    if (!$product) {
        continue;
    }

    $quantity = max(1, (int) ($item['quantity'] ?? 1));
    $lineTotal = $quantity * (float) $product['price'];
    $total += $lineTotal;

    $items[] = [
        'product_id' => (int) $item['product_id'],
        'product' => $product,
        'quantity' => $quantity,
        'line_total' => $lineTotal,
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="logo.png">
<link rel="shortcut icon" href="favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | PureGlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --cream:#fcf8f2;
  --warm-white:#fffdf9;
  --soft-sand:#f1e7db;
  --espresso:#2e2822;
  --walnut:#6c5241;
  --taupe:#9f8b7c;
  --gold:#d5b089;
  --gold-light:#f2e1c9;
  --ink-blue:#3c6fd1;
  --white:#fff;
  --display:"Cormorant Garamond",Georgia,serif;
  --body:"Manrope",sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  min-height:100vh;
  font-family:var(--body);
  color:var(--espresso);
  background:
    radial-gradient(circle at top left,rgba(213,176,137,.2),transparent 30%),
    radial-gradient(circle at top right,rgba(60,111,209,.14),transparent 26%),
    linear-gradient(180deg,#fffdf9 0%,#fdf9f3 54%,#f8f3ed 100%);
  padding:28px;
}
a{color:inherit;text-decoration:none}
.shell{width:min(1120px,100%);margin:0 auto}
.topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:28px}
.brand{display:flex;align-items:center;gap:12px;font-weight:800}
.brand-mark{width:44px;height:44px;border-radius:14px;display:grid;place-items:center;background:linear-gradient(160deg,var(--white),var(--gold-light));font-family:var(--display);font-size:1.35rem;box-shadow:0 12px 35px rgba(46,40,34,.08);overflow:hidden}
.brand-mark img{width:100%;height:100%;object-fit:cover;display:block}
.back-link{padding:12px 18px;border-radius:14px;background:rgba(255,255,255,.72);border:1px solid rgba(46,40,34,.1);font-weight:800;transition:.25s}
.back-link:hover{transform:translateY(-2px);border-color:rgba(213,176,137,.45);box-shadow:0 12px 24px rgba(213,176,137,.18)}
.hero{margin:28px 0 24px}
.kicker{font-size:.75rem;letter-spacing:.22em;text-transform:uppercase;color:var(--ink-blue);font-weight:800}
h1{font-family:var(--display);font-size:clamp(2.8rem,6vw,5.4rem);font-weight:500;line-height:.95;margin-top:10px}
.layout{display:grid;grid-template-columns:1fr 420px;gap:22px;align-items:start}
.panel{background:rgba(255,255,255,.86);border:1px solid rgba(46,40,34,.08);border-radius:28px;box-shadow:0 20px 60px rgba(46,40,34,.12);padding:24px}
.panel h2{font-family:var(--display);font-size:2rem;font-weight:500;margin-bottom:16px}
.cart-list{display:grid;gap:14px}
.cart-item{display:grid;grid-template-columns:78px 1fr auto;gap:14px;align-items:center;padding:12px;border-radius:20px;background:rgba(252,248,242,.78);border:1px solid rgba(46,40,34,.07)}
.cart-item img{width:78px;height:78px;object-fit:cover;border-radius:16px;background:var(--soft-sand)}
.cart-item h3{font-size:1rem;margin-bottom:4px}
.cart-item p{color:var(--taupe);font-size:.9rem}
.cart-actions{display:grid;justify-items:end;gap:8px}
.line-total{font-weight:900;color:var(--walnut);white-space:nowrap}
.remove-item{width:auto;padding:9px 12px;border-radius:12px;background:rgba(255,255,255,.82);border:1px solid rgba(180,35,24,.18);color:#b42318;font-size:.78rem;font-weight:900;box-shadow:none}
.remove-item:hover{transform:translateY(-1px);border-color:rgba(180,35,24,.35);box-shadow:0 10px 18px rgba(180,35,24,.1)}
.remove-item:disabled{opacity:.55;cursor:not-allowed;transform:none}
.summary-row{display:flex;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid rgba(46,40,34,.08);color:var(--taupe);font-weight:700}
.summary-row.total{border-bottom:0;color:var(--espresso);font-size:1.35rem;font-weight:900}
form{display:grid;gap:12px;margin-top:16px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
input{width:100%;padding:14px 15px;border-radius:16px;border:1px solid rgba(46,40,34,.12);background:rgba(252,248,242,.9);outline:none;color:var(--espresso)}
input:focus{border-color:rgba(60,111,209,.45);box-shadow:0 0 0 4px rgba(60,111,209,.08);background:var(--white)}
.email-option{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:16px;background:rgba(238,244,255,.72);color:var(--espresso);font-size:.9rem;font-weight:800;line-height:1.45}
.email-option input{width:18px;height:18px;margin-top:2px;accent-color:var(--ink-blue)}
button{width:100%;padding:16px 20px;border:0;border-radius:18px;background:linear-gradient(135deg,#f7ead8 0%,#e8c9a7 50%,#f7ead8 100%);color:#5b4332;font-size:1rem;font-weight:900;cursor:pointer;box-shadow:0 0 0 1px rgba(255,217,120,.7),0 12px 24px rgba(255,217,120,.26);transition:.25s}
button:hover{transform:translateY(-2px);box-shadow:0 0 0 1px rgba(255,217,120,1),0 18px 34px rgba(255,217,120,.42)}
.status{min-height:24px;margin-top:12px;color:#b42318;font-weight:800}
.empty{padding:28px;border-radius:22px;background:rgba(252,248,242,.78);color:var(--taupe);line-height:1.7}
.empty a{display:inline-flex;align-items:center;justify-content:center;margin-top:14px;padding:13px 18px;border-radius:16px;background:linear-gradient(135deg,#f7ead8 0%,#e8c9a7 50%,#f7ead8 100%);color:#5b4332;font-weight:900;box-shadow:0 0 0 1px rgba(255,217,120,.7),0 12px 24px rgba(255,217,120,.26);transition:.25s}
.empty a:hover{transform:translateY(-2px);box-shadow:0 0 0 1px rgba(255,217,120,1),0 18px 34px rgba(255,217,120,.42)}
@media(max-width:860px){
  body{padding:18px}
  .layout{grid-template-columns:1fr}
  .cart-item{grid-template-columns:64px 1fr}
  .cart-actions{grid-column:2;justify-items:start}
  .form-row{grid-template-columns:1fr}
}
</style>
</head>
<body>
<main class="shell">
  <div class="topbar">
    <a class="brand" href="index.html">
      <span class="brand-mark"><img src="logo.png" alt="PureGlow logo"></span>
      <span>PureGlow</span>
    </a>
    <a class="back-link" href="index.html#catalog">Back to products</a>
  </div>

  <section class="hero">
    <div class="kicker">Secure checkout</div>
    <h1>Your skincare order</h1>
  </section>

  <?php if (empty($items)): ?>
    <div class="panel empty">
      Your cart is empty. Add a product first, then come back to checkout.
      <br>
      <a href="index.html#catalog">Browse products</a>
    </div>
  <?php else: ?>
    <div class="layout">
      <section class="panel">
        <h2>Cart</h2>
        <div class="cart-list">
          <?php foreach ($items as $item): ?>
            <?php
              $product = $item['product'];
              $image = $product['images'][0] ?? '';
            ?>
            <article class="cart-item">
              <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
              <div>
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars($product['brand'], ENT_QUOTES, 'UTF-8') ?> · Qty <?= (int) $item['quantity'] ?></p>
              </div>
              <div class="cart-actions">
                <div class="line-total"><?= htmlspecialchars($product['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $item['line_total'], 2) ?></div>
                <button class="remove-item" type="button" data-product-id="<?= (int) $item['product_id'] ?>">Remove</button>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <aside class="panel">
        <h2>Payment</h2>
        <div class="summary-row"><span>Subtotal</span><strong>EUR <?= number_format($total, 2) ?></strong></div>
        <div class="summary-row"><span>Shipping</span><strong>Free</strong></div>
        <div class="summary-row total"><span>Total</span><strong>EUR <?= number_format($total, 2) ?></strong></div>

        <form id="paymentForm">
          <input name="card_number" placeholder="4242 4242 4242 4242" value="4242 4242 4242 4242" required>
          <div class="form-row">
            <input name="expiry" placeholder="12/30" value="12/30" required>
            <input name="cvv" placeholder="123" value="123" required>
          </div>
          <input name="street" placeholder="Street">
          <div class="form-row">
            <input name="city" placeholder="City">
            <input name="postal_code" placeholder="Postal Code">
          </div>
          <input name="country" placeholder="Country">
          <label class="email-option">
            <input type="checkbox" name="invoice_email_requested" value="1" checked>
            <span>Send my invoice by email after the admin confirms the order.</span>
          </label>
          <button type="submit">Pay Now</button>
          <p class="status" id="status"></p>
        </form>
      </aside>
    </div>
  <?php endif; ?>
</main>

<script>
const paymentForm = document.getElementById("paymentForm");
const removeButtons = document.querySelectorAll(".remove-item");

removeButtons.forEach(button => {
  button.addEventListener("click", async () => {
    const productId = Number(button.dataset.productId);
    if (!productId) return;

    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = "Removing...";

    try {
      const res = await fetch("api/cart.php", {
        method: "DELETE",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({product_id: productId})
      });

      const data = await res.json();

      if (!res.ok) {
        alert(data.error || "Product could not be removed.");
        button.disabled = false;
        button.textContent = originalText;
        return;
      }

      window.location.reload();
    } catch {
      alert("Network error. Please try again.");
      button.disabled = false;
      button.textContent = originalText;
    }
  });
});

if (paymentForm) {
  paymentForm.addEventListener("submit", async e => {
    e.preventDefault();

    const status = document.getElementById("status");
    const button = paymentForm.querySelector("button");
    const payload = Object.fromEntries(new FormData(paymentForm).entries());
    payload.invoice_email_requested = paymentForm.elements.invoice_email_requested.checked ? "1" : "0";

    status.textContent = "";
    button.disabled = true;
    button.textContent = "Processing...";

    try {
      const res = await fetch("api/fake-payment.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      if (res.ok) {
        window.location.href = "payment-success.php";
        return;
      }

      status.textContent = data.error || "Payment failed";
    } catch {
      status.textContent = "Network error. Please try again.";
    } finally {
      button.disabled = false;
      button.textContent = "Pay Now";
    }
  });
}
</script>
</body>
</html>

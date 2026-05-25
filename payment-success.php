<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="logo.png">
<link rel="shortcut icon" href="favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Successful | PureGlow</title>
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
  --sage:#6f8a7a;
  --sage-light:#e8f1eb;
  --white:#fff;
  --display:"Cormorant Garamond",Georgia,serif;
  --body:"Manrope",sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  min-height:100vh;
  display:grid;
  place-items:center;
  padding:28px;
  font-family:var(--body);
  color:var(--espresso);
  background:
    radial-gradient(circle at top left,rgba(213,176,137,.22),transparent 30%),
    radial-gradient(circle at top right,rgba(213,176,137,.16),transparent 26%),
    linear-gradient(180deg,#fffdf9 0%,#fdf9f3 54%,#f8f3ed 100%);
}
a{color:inherit;text-decoration:none}
.success-shell{
  width:min(860px,100%);
}
.brand{
  display:inline-flex;
  align-items:center;
  gap:12px;
  margin-bottom:22px;
  font-weight:800;
}
.brand-mark{
  width:44px;
  height:44px;
  display:grid;
  place-items:center;
  border-radius:14px;
  background:linear-gradient(160deg,var(--white),var(--gold-light));
  font-family:var(--display);
  font-size:1.35rem;
  box-shadow:0 12px 35px rgba(46,40,34,.08);
  overflow:hidden;
}
.brand-mark img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
.panel{
  position:relative;
  overflow:hidden;
  padding:clamp(28px,5vw,56px);
  border-radius:30px;
  background:rgba(255,255,255,.88);
  border:1px solid rgba(46,40,34,.08);
  box-shadow:0 24px 70px rgba(46,40,34,.13);
}
.panel::before{
  content:"";
  position:absolute;
  inset:0 0 auto;
  height:8px;
  background:linear-gradient(90deg,var(--gold-light),var(--gold),var(--gold-light));
}
.success-icon{
  width:76px;
  height:76px;
  display:grid;
  place-items:center;
  margin-bottom:22px;
  border-radius:24px;
  color:#5b4332;
  background:linear-gradient(135deg,#f7ead8 0%,#e8c9a7 50%,#f7ead8 100%);
  box-shadow:
    0 0 0 1px rgba(255,217,120,.7),
    0 14px 34px rgba(255,217,120,.32);
}
.success-icon svg{
  width:34px;
  height:34px;
}
.kicker{
  margin-bottom:10px;
  font-size:.75rem;
  letter-spacing:.22em;
  text-transform:uppercase;
  color:var(--sage);
  font-weight:800;
}
h1{
  max-width:680px;
  font-family:var(--display);
  font-size:clamp(3rem,7vw,5.4rem);
  font-weight:500;
  line-height:.95;
  color:var(--espresso);
}
.copy{
  max-width:560px;
  margin-top:18px;
  color:var(--taupe);
  font-size:1rem;
  line-height:1.8;
}
.email-note{
  display:flex;
  align-items:flex-start;
  gap:12px;
  max-width:560px;
  margin-top:22px;
  padding:14px 16px;
  border-radius:18px;
  background:linear-gradient(135deg,rgba(247,234,216,.86),rgba(255,253,249,.92));
  border:1px solid rgba(213,176,137,.34);
  color:#6c5241;
  box-shadow:0 12px 28px rgba(213,176,137,.12);
}
.email-note-mark{
  width:34px;
  height:34px;
  display:grid;
  place-items:center;
  flex:0 0 auto;
  border-radius:12px;
  background:rgba(213,176,137,.18);
  color:#6c5241;
  font-weight:900;
}
.email-note-text{
  display:grid;
  gap:3px;
}
.email-note-title{
  color:var(--espresso);
  font-size:.82rem;
  font-weight:900;
  letter-spacing:.08em;
  text-transform:uppercase;
}
.email-note-copy{
  color:var(--walnut);
  font-size:.92rem;
  font-weight:700;
  line-height:1.6;
}
.actions{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  margin-top:30px;
}
.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-height:52px;
  padding:0 22px;
  border-radius:16px;
  font-weight:900;
  transition:.25s;
}
.btn-primary{
  color:#5b4332;
  background:linear-gradient(135deg,#f7ead8 0%,#e8c9a7 50%,#f7ead8 100%);
  box-shadow:
    0 0 0 1px rgba(255,217,120,.7),
    0 12px 24px rgba(255,217,120,.26);
}
.btn-secondary{
  color:#5b4332;
  background:rgba(255,253,249,.86);
  border:1px solid rgba(213,176,137,.36);
}
.btn:hover{
  transform:translateY(-2px);
}
.btn-primary:hover{
  box-shadow:
    0 0 0 1px rgba(255,217,120,1),
    0 18px 34px rgba(255,217,120,.42);
}
.btn-secondary:hover{
  border-color:rgba(213,176,137,.62);
  box-shadow:0 12px 24px rgba(213,176,137,.18);
}
@media(max-width:620px){
  body{padding:18px}
  .panel{border-radius:24px}
  .actions{display:grid}
  .btn{width:100%}
}
</style>
</head>
<body>
<main class="success-shell">
  <a class="brand" href="index.html">
    <span class="brand-mark"><img src="logo.png" alt="PureGlow logo"></span>
    <span>PureGlow</span>
  </a>

  <section class="panel">
    <div class="success-icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="kicker">Payment completed</div>
    <h1>Your skincare order is confirmed</h1>
    <p class="copy">
      Thank you for shopping with PureGlow. Your demo payment was completed successfully and your cart has been cleared.
    </p>
    <div class="email-note">
      <span class="email-note-mark" aria-hidden="true">✓</span>
      <span class="email-note-text">
        <span class="email-note-title">Invoice email</span>
        <span class="email-note-copy">If you selected this option, your invoice will be emailed after the admin confirms your order.</span>
      </span>
    </div>
    <div class="actions">
      <a class="btn btn-primary" href="index.html#catalog">Continue shopping</a>
      <a class="btn btn-secondary" href="index.html">Back to home</a>
    </div>
  </section>
</main>
</body>
</html>

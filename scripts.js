// Header scroll
const header = document.getElementById("header");
if (header) {
  window.addEventListener("scroll", () => {
    header.classList.toggle("scrolled", window.scrollY > 40);
  });
}

// Hamburger
const hamburger = document.getElementById("hamburger");
const mobilePanel = document.getElementById("mobilePanel");

if (hamburger && mobilePanel) {
  hamburger.addEventListener("click", () => {
    const open = !mobilePanel.classList.contains("open");
    hamburger.classList.toggle("open", open);
    mobilePanel.classList.toggle("open", open);
    document.body.classList.toggle("menu-open", open);
  });

  mobilePanel.querySelectorAll("a").forEach(a =>
    a.addEventListener("click", () => {
      hamburger.classList.remove("open");
      mobilePanel.classList.remove("open");
      document.body.classList.remove("menu-open");
    })
  );
}

// Fade up observer
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) e.target.classList.add("visible");
  });
}, { threshold: 0.12 });

document.querySelectorAll(".fade-up").forEach(el => observer.observe(el));

// Hero 3D tilt
const heroVisual = document.getElementById("heroVisual");

if (heroVisual) {
  heroVisual.addEventListener("mousemove", e => {
    const r = heroVisual.getBoundingClientRect();
    const rx = ((e.clientY - r.top) / r.height - 0.5) * -7;
    const ry = ((e.clientX - r.left) / r.width - 0.5) * 7;
    const wrap = heroVisual.querySelector(".hero-photo-wrap");
    if (wrap) {
      wrap.style.transform = `perspective(1200px) rotateX(${rx}deg) rotateY(${ry}deg)`;
    }
  });

  heroVisual.addEventListener("mouseleave", () => {
    const wrap = heroVisual.querySelector(".hero-photo-wrap");
    if (wrap) {
      wrap.style.transform = "perspective(1200px) rotateX(0deg) rotateY(0deg)";
    }
  });
}

// Counters
const counterObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;

    const el = e.target;
    const target = Number(el.dataset.counter);
    let cur = 0;
    const step = Math.max(1, Math.ceil(target / 30));

    const iv = setInterval(() => {
      cur += step;
      if (cur >= target) {
        cur = target;
        clearInterval(iv);
      }
      el.textContent = cur + (target > 9 ? "+" : "");
    }, 40);

    counterObs.unobserve(el);
  });
}, { threshold: 0.5 });

document.querySelectorAll("[data-counter]").forEach(el => counterObs.observe(el));

async function updateAuthButtons() {
  const authButtons = document.querySelector(".auth-buttons");
  if (!authButtons) return;

  try {
    const response = await fetch("api/auth.php?action=status");
    const data = await response.json();

    if (!data.authenticated) {
      authButtons.innerHTML = `
        <a href="signin.php" class="signin-btn">Sign In</a>
        <a href="signup.php" class="signup-btn">Sign Up</a>
      `;
      return;
    }

    if (data.user?.role === "admin") {
      window.location.href = "admin/dashboard.php";
      return;
    }

    authButtons.innerHTML = `
      <span class="user-chip">${data.user.name}</span>
      <button class="logout-btn-site" type="button">Log Out</button>
    `;

    authButtons.querySelector(".logout-btn-site").addEventListener("click", async () => {
      await fetch("api/auth.php?action=logout", { method: "POST" });
      window.location.href = "index.html";
    });
  } catch (error) {
    console.error("Auth status error:", error);
  }
}

updateAuthButtons();

// Load products from database
async function loadProducts() {
  const grid = document.getElementById("productsGrid");
  if (!grid) return;

  try {
    const response = await fetch("api/products.php");
    const data = await response.json();
    const products = data.products || [];

    grid.innerHTML = "";

    products.forEach(product => {
      let images = [];

      if (Array.isArray(product.images)) {
        images = product.images;
      } else {
        try {
          images = JSON.parse(product.images || "[]");
        } catch {
          images = [];
        }
      }

      const image = images[0] || "";
      const discountPercent = Number(product.discount_percent || 0);

      grid.innerHTML += `
        <article class="product-card fade-up visible" data-category="${product.category}" data-promotion="${discountPercent > 0 ? "true" : "false"}">
         <div class="product-media">

  ${discountPercent > 0 ? `
    <span class="product-discount">
      -${discountPercent}%
    </span>
  ` : ""}

  <button class="product-image-button" type="button" aria-label="Open larger image of ${product.name}">
    <img src="${image}" alt="${product.name}">
  </button>
  <span class="product-brand-badge">${product.brand}</span>
</div>

          <div class="product-info">
            <div class="product-category">${product.category}</div>

            <h3 class="product-name">${product.name}</h3>

            <p class="product-desc">${product.description}</p>

            <div class="product-footer">

  <span class="product-price">
    ${product.currency} ${product.price}
  </span>

  <div class="quantity-control" aria-label="Choose quantity">
    <button class="quantity-btn quantity-minus" type="button" aria-label="Decrease quantity">-</button>
    <input class="quantity-input" type="number" value="1" min="1" max="${product.stock || 99}" inputmode="numeric" aria-label="Quantity">
    <button class="quantity-btn quantity-plus" type="button" aria-label="Increase quantity">+</button>
  </div>

  <button class="add-to-cart" type="button" data-product-id="${product.id}">
  Add To Cart
</button>

</div>
          </div>
        </article>
      `;
    });

    setupFilters();

  } catch (error) {
    console.error("Products error:", error);
  }
}

loadProducts();

async function addToCart(productId, quantity = 1) {
  if (!productId) {
    alert("Product could not be added to cart.");
    return;
  }

  const safeQuantity = Math.max(1, Number(quantity) || 1);

  try {
    const authResponse = await fetch("api/auth.php?action=status");
    const auth = await authResponse.json();

    if (!auth.authenticated) {
      window.location.href = "signin.php";
      return;
    }

    const response = await fetch("api/cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ product_id: productId, quantity: safeQuantity }),
    });

    const result = await response.json();

    if (!response.ok) {
      alert(result.error || "Product could not be added to cart.");
      return;
    }

    showToast(result.message || "Product added to cart.");
  } catch (error) {
    console.error("Cart error:", error);
    alert("Network error. Please try again.");
  }
}

window.addToCart = addToCart;

document.addEventListener("click", async event => {
  const quantityButton = event.target.closest(".quantity-btn");
  if (quantityButton) {
    const control = quantityButton.closest(".quantity-control");
    const input = control?.querySelector(".quantity-input");
    if (!input) return;

    const min = Number(input.min) || 1;
    const max = Number(input.max) || 99;
    const current = Number(input.value) || min;
    const next = quantityButton.classList.contains("quantity-plus") ? current + 1 : current - 1;
    input.value = Math.min(max, Math.max(min, next));
    return;
  }

  const imageButton = event.target.closest(".product-image-button");
  if (imageButton && lightbox && lightboxImage) {
    const image = imageButton.querySelector("img");
    if (!image?.src) return;

    lightboxImage.src = image.src;
    lightboxImage.alt = image.alt || "Product image";
    lightbox.classList.add("open");
    document.body.classList.add("lightbox-open");
    return;
  }

  const button = event.target.closest(".add-to-cart");
  if (!button) return;

  const productId = Number(button.dataset.productId);
  const card = button.closest(".product-card");
  const quantityInput = card?.querySelector(".quantity-input");
  const quantity = Math.max(1, Number(quantityInput?.value) || 1);

  button.disabled = true;
  button.classList.add("loading");

  await addToCart(productId, quantity);

  button.disabled = false;
  button.classList.remove("loading");
});

document.addEventListener("input", event => {
  const input = event.target.closest(".quantity-input");
  if (!input) return;

  const min = Number(input.min) || 1;
  const max = Number(input.max) || 99;
  const value = Number(input.value) || min;
  input.value = Math.min(max, Math.max(min, value));
});

function showToast(message) {
  let toast = document.getElementById("siteToast");

  if (!toast) {
    toast = document.createElement("div");
    toast.id = "siteToast";
    toast.className = "site-toast";
    document.body.appendChild(toast);
  }

  toast.textContent = message;
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 2600);
}

// Product filter
function setupFilters() {
  document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.dataset.filter;

      document.querySelectorAll(".product-card").forEach(card => {
        const isPromotion = card.dataset.promotion === "true";
        const shouldHide =
          filter !== "all" &&
          !(filter === "promotions" ? isPromotion : card.dataset.category === filter);

        card.classList.toggle("hidden", shouldHide);
      });
    };
  });
}

// Lightbox
const lightbox = document.getElementById("lightbox");
const lightboxImage = document.getElementById("lightboxImage");
const lightboxClose = document.getElementById("lightboxClose");

if (lightbox && lightboxImage && lightboxClose) {
  lightboxClose.addEventListener("click", () => {
    lightbox.classList.remove("open");
    document.body.classList.remove("lightbox-open");
  });

  lightbox.addEventListener("click", e => {
    if (e.target === lightbox) {
      lightbox.classList.remove("open");
      document.body.classList.remove("lightbox-open");
    }
  });
}

// Booking form
const bookingForm = document.getElementById("bookingForm");
const bookingSuccess = document.getElementById("bookingSuccess");
const bookingDate = document.getElementById("bookingDate");

if (bookingForm && bookingSuccess && bookingDate) {
  bookingDate.min = new Date().toISOString().split("T")[0];

  bookingForm.addEventListener("submit", async e => {
    e.preventDefault();

    const payload = {
      name: document.getElementById("bookingName").value.trim(),
      phone: document.getElementById("bookingPhone").value.trim(),
      email: document.getElementById("bookingEmail").value.trim(),
      skin_type: document.getElementById("skinType").value,
      date: bookingDate.value,
      time: document.getElementById("bookingTime").value,
    };

    try {
      const response = await fetch("api/booking.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      bookingSuccess.textContent = result.message || result.error || "Done.";
      bookingSuccess.classList.add("show");

      if (response.ok) {
        bookingForm.reset();
        bookingDate.min = new Date().toISOString().split("T")[0];
      }

      setTimeout(() => bookingSuccess.classList.remove("show"), 4000);
    } catch {
      bookingSuccess.textContent = "Network error. Please try again later.";
      bookingSuccess.classList.add("show");
    }
  });
}

// Contact form
const contactForm = document.getElementById("contactForm");

if (contactForm) {
  contactForm.addEventListener("submit", async e => {
    e.preventDefault();

    const form = e.target;
    const fields = form.querySelectorAll("input, textarea");
    const [firstName, lastName, emailField, messageField] = fields;

    const payload = {
      name: `${firstName.value.trim()} ${lastName.value.trim()}`.trim(),
      email: emailField.value.trim(),
      message: messageField.value.trim(),
    };

    const btn = document.getElementById("contactSubmit");
    const contactSuccess = document.getElementById("contactSuccess");

    try {
      const response = await fetch("api/contact.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (contactSuccess) {
        contactSuccess.textContent = result.message || result.error || "Done.";
        contactSuccess.classList.add("show");
      }

      if (response.ok) {
        if (btn) btn.textContent = "Message sent ✓";
        form.reset();

        setTimeout(() => {
          if (btn) btn.textContent = "Send message";
          if (contactSuccess) contactSuccess.classList.remove("show");
        }, 3500);
      }
    } catch {
      if (contactSuccess) {
        contactSuccess.textContent = "Network error. Please try again later.";
        contactSuccess.classList.add("show");
      }
    }
  });
}

// Newsletter
const newsletterForm = document.getElementById("newsletterForm");

if (newsletterForm) {
  newsletterForm.addEventListener("submit", async e => {
    e.preventDefault();

    const email = document.getElementById("newsletterEmail").value.trim();
    const status = document.getElementById("newsletterStatus");

    if (!email) {
      if (status) status.textContent = "Please enter a valid email address.";
      return;
    }

    try {
      const response = await fetch("api/newsletter.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });

      const result = await response.json();

      if (status) status.textContent = result.message || "Subscribed successfully.";

      if (response.ok) e.target.reset();
    } catch {
      if (status) status.textContent = "Network error. Please try again later.";
    }
  });
}

// Escape key
document.addEventListener("keydown", e => {
  if (e.key === "Escape") {
    if (lightbox) lightbox.classList.remove("open");
    document.body.classList.remove("lightbox-open", "menu-open");
    if (hamburger) hamburger.classList.remove("open");
    if (mobilePanel) mobilePanel.classList.remove("open");
  }
});
const ambassadorModal = document.getElementById("ambassadorModal");
const ambassadorModalName = document.getElementById("ambassadorModalName");
const ambassadorModalRole = document.getElementById("ambassadorModalRole");
const ambassadorModalDesc = document.getElementById("ambassadorModalDesc");
const ambassadorClose = document.querySelector(".ambassador-modal-close");

document.querySelectorAll(".ambassador-learn").forEach(button => {
  button.addEventListener("click", () => {
    if (!ambassadorModal || !ambassadorModalName || !ambassadorModalRole || !ambassadorModalDesc) {
      return;
    }

    ambassadorModalName.textContent = button.dataset.name;
    ambassadorModalRole.textContent = button.dataset.role;
    ambassadorModalDesc.textContent = button.dataset.desc;
    ambassadorModal.classList.add("open");
  });
});

if (ambassadorClose) {
  ambassadorClose.addEventListener("click", () => {
    ambassadorModal.classList.remove("open");
  });
}

if (ambassadorModal) {
  ambassadorModal.addEventListener("click", e => {
    if (e.target === ambassadorModal) {
      ambassadorModal.classList.remove("open");
    }
  });
}
/* AMBASSADOR FLIP CARDS */

const learnButtons = document.querySelectorAll('.ambassador-learn');
const backButtons = document.querySelectorAll('.ambassador-back-btn');

learnButtons.forEach(button => {
  button.addEventListener('click', () => {
    const card = button.closest('.ambassador-card');
    card.classList.add('flipped');
  });
});

backButtons.forEach(button => {
  button.addEventListener('click', () => {
    const card = button.closest('.ambassador-card');
    card.classList.remove('flipped');
  });
});

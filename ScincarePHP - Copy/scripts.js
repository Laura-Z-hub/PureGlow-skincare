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

      grid.innerHTML += `
        <article class="product-card fade-up visible" data-category="${product.category}">
          <div class="product-media">
            <img src="${image}" alt="${product.name}">
            <span class="product-brand-badge">${product.brand}</span>
          </div>

          <div class="product-info">
            <div class="product-category">${product.category}</div>

            <h3 class="product-name">${product.name}</h3>

            <p class="product-desc">${product.description}</p>

            <div class="product-footer">
              <span class="product-price">${product.currency} ${product.price}</span>
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

// Product filter
function setupFilters() {
  document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.dataset.filter;

      document.querySelectorAll(".product-card").forEach(card => {
        card.classList.toggle(
          "hidden",
          filter !== "all" && card.dataset.category !== filter
        );
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
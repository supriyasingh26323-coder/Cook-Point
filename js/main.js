// ===== CART MANAGEMENT =====
function getCart() {
  return JSON.parse(localStorage.getItem("cp_cart")) || [];
}

function saveCart(cart) {
  localStorage.setItem("cp_cart", JSON.stringify(cart));
  updateCartCount();
}

function updateCartCount() {
  const cart = getCart();
  const total = cart.reduce((sum, i) => sum + i.quantity, 0);
  document.querySelectorAll(".cart-count").forEach((el) => {
    el.textContent = total;
  });
}

function addToCart(id, name, price) {
  let cart = getCart();
  const existing = cart.find((i) => i.id == id);
  if (existing) {
    existing.quantity++;
  } else {
    cart.push({ id, name, price: parseFloat(price), quantity: 1 });
  }
  saveCart(cart);
  showToast(name + " added to cart!");
}

function showToast(msg) {
  let toast = document.getElementById("toast");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "toast";
    toast.style.cssText =
      "position:fixed;bottom:30px;right:30px;background:#ff5722;color:white;padding:12px 22px;border-radius:50px;font-size:14px;z-index:9999;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:opacity 0.4s;";
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = "1";
  setTimeout(() => {
    toast.style.opacity = "0";
  }, 2500);
}

// ===== LOAD MENU =====
function loadMenu(category = "All", limit = 0) {
  const grid = document.getElementById("menuGrid");
  if (!grid) return;
  grid.innerHTML = '<p style="text-align:center;color:#999;padding:30px;">Loading...</p>';

  fetch(`php/get_menu.php?category=${category}`)
    .then((r) => r.json())
    .then((items) => {
      if (items.length === 0) {
        grid.innerHTML = '<p style="text-align:center;color:#999;padding:30px;">No items found.</p>';
        return;
      }
      // limit items shown (used on homepage)
      if (limit > 0) items = items.slice(0, limit);

      grid.innerHTML = items
        .map(
          (item) => `
        <div class="menu-card">
          <img
            loading="lazy"
            src="./images/${item.image}"
            alt="${item.name}"
            width="300"
            height="180"
            onerror="this.src='https://via.placeholder.com/300x180/ff5722/ffffff?text=${encodeURIComponent(item.name)}'">
          <div class="card-body">
            <h3>${item.name}</h3>
            <p>${item.description}</p>
            <div class="card-footer">
              <span class="price">₹${parseFloat(item.price).toFixed(2)}</span>
              <button class="add-btn" onclick="addToCart(${item.id},'${item.name}',${item.price})">+ Add</button>
            </div>
          </div>
        </div>`
        )
        .join("");
    })
    .catch(() => {
      grid.innerHTML = '<p style="text-align:center;color:red;padding:30px;">Failed to load menu. Check server.</p>';
    });
}

// ===== LOAD DYNAMIC CATEGORY TABS FROM DB =====
function loadCategoryTabs() {
  const tabsContainer = document.getElementById("categoryTabs");
  if (!tabsContainer) return;

  fetch("php/get_menu.php?type=categories")
    .then((r) => r.json())
    .then((cats) => {
      tabsContainer.innerHTML = cats
        .map((cat, i) =>
          `<button class="tab-btn${i === 0 ? ' active' : ''}" data-cat="${cat.name}">${cat.icon} ${cat.name}</button>`
        )
        .join("");
      initTabs();
      loadMenu("All");
    })
    .catch(() => {
      // fallback: just load menu without tabs
      loadMenu("All");
    });
}

// ===== CATEGORY TABS =====
function initTabs() {
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      loadMenu(this.dataset.cat);
    });
  });
}

// ===== RENDER CART PAGE =====
function renderCart() {
  const tbody = document.getElementById("cartBody");
  const summaryDiv = document.getElementById("cartSummary");
  if (!tbody) return;

  const cart = getCart();

  if (cart.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#999;">Your cart is empty. <a href="menu.html" style="color:#ff5722;">Browse Menu</a></td></tr>';
    if (summaryDiv) summaryDiv.style.display = "none";
    return;
  }

  let total = 0;
  tbody.innerHTML = cart
    .map((item) => {
      const sub = item.price * item.quantity;
      total += sub;
      return `
      <tr>
        <td><strong>${item.name}</strong></td>
        <td>₹${item.price.toFixed(2)}</td>
        <td>
          <button class="qty-btn" onclick="changeQty(${item.id},-1)">−</button>
          <span class="qty-display">${item.quantity}</span>
          <button class="qty-btn" onclick="changeQty(${item.id},1)">+</button>
        </td>
        <td>₹${sub.toFixed(2)}</td>
        <td><button class="remove-btn" onclick="removeItem(${item.id})">Remove</button></td>
      </tr>`;
    })
    .join("");

  if (summaryDiv) {
    summaryDiv.style.display = "block";
    summaryDiv.innerHTML = `
      <h3>Total: ₹${total.toFixed(2)}</h3>
      <a href="checkout.html" class="btn-primary">Proceed to Checkout</a>`;
  }
}

function changeQty(id, delta) {
  let cart = getCart();
  const item = cart.find((i) => i.id == id);
  if (item) {
    item.quantity += delta;
    if (item.quantity <= 0) cart = cart.filter((i) => i.id != id);
  }
  saveCart(cart);
  renderCart();
}

function removeItem(id) {
  let cart = getCart().filter((i) => i.id != id);
  saveCart(cart);
  renderCart();
}

// ===== RENDER CHECKOUT =====
function renderCheckout() {
  const list = document.getElementById("orderList");
  const totalEl = document.getElementById("orderTotal");
  if (!list) return;

  const cart = getCart();
  if (cart.length === 0) {
    window.location.href = "cart.html";
    return;
  }

  let total = 0;
  list.innerHTML = cart
    .map((item) => {
      const sub = item.price * item.quantity;
      total += sub;
      return `<li><span>${item.name} × ${item.quantity}</span><span>₹${sub.toFixed(2)}</span></li>`;
    })
    .join("");

  if (totalEl) totalEl.innerHTML = `<span>Total</span><span>₹${total.toFixed(2)}</span>`;
  document.getElementById("cartTotal").value = total.toFixed(2);
}

// ===== REGISTER =====
function handleRegister(e) {
  e.preventDefault();
  const alertBox = document.getElementById("regAlert");
  const data = new FormData(e.target);

  fetch("php/register.php", { method: "POST", body: data })
    .then((r) => r.text())
    .then((res) => {
      alertBox.style.display = "block";
      if (res.trim() === "success") {
        alertBox.className = "alert alert-success";
        alertBox.textContent = "Registered successfully! Redirecting to login...";
        setTimeout(() => (window.location.href = "login.html"), 1500);
      } else if (res.trim() === "exists") {
        alertBox.className = "alert alert-danger";
        alertBox.textContent = "Email already registered!";
      } else {
        alertBox.className = "alert alert-danger";
        alertBox.textContent = "Registration failed. Try again.";
      }
    });
}

// ===== LOGIN =====
function handleLogin(e) {
  e.preventDefault();
  const alertBox = document.getElementById("loginAlert");
  const data = new FormData(e.target);

  fetch("php/login.php", { method: "POST", body: data })
    .then((r) => r.text())
    .then((res) => {
      alertBox.style.display = "block";
      if (res.trim() === "success") {
        alertBox.className = "alert alert-success";
        alertBox.textContent = "Login successful! Redirecting...";
        setTimeout(() => (window.location.href = "index.html"), 1200);
      } else {
        alertBox.className = "alert alert-danger";
        alertBox.textContent = "Invalid email or password!";
      }
    });
}

// ===== PLACE ORDER =====
function handleCheckout(e) {
  e.preventDefault();
  const data = new FormData(e.target);
  data.append("cart", JSON.stringify(getCart()));

  fetch("php/place_order.php", { method: "POST", body: data })
    .then((r) => r.text())
    .then((res) => {
      if (res.trim() === "success") {
        localStorage.removeItem("cp_cart");
        window.location.href = "order-success.html";
      } else if (res.trim() === "login_required") {
        window.location.href = "login.html";
      } else {
        alert("Order failed. Please try again.");
      }
    });
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", function () {
  updateCartCount();

  const regForm = document.getElementById("registerForm");
  if (regForm) regForm.addEventListener("submit", handleRegister);

  const loginForm = document.getElementById("loginForm");
  if (loginForm) loginForm.addEventListener("submit", handleLogin);

  const checkoutForm = document.getElementById("checkoutForm");
  if (checkoutForm) checkoutForm.addEventListener("submit", handleCheckout);

  // menu.html — load dynamic tabs from DB
  if (document.getElementById("categoryTabs")) {
    loadCategoryTabs();
  }

  renderCart();
  renderCheckout();
});

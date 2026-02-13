/**
 * Purchase Module
 * Barcode scanning, product search, cart management, purchase processing
 */
(function () {
  "use strict";

  /* ── State ──────────────────────────────────── */
  let cart = [];
  let searchTimeout = null;

  /* ── DOM References ─────────────────────────── */
  const $barcode = document.getElementById("barcodeInput");
  const $dropdown = document.getElementById("searchDropdown");
  const $cartBody = document.getElementById("cartBody");
  const $emptyRow = document.getElementById("emptyRow");
  const $totalAmount = document.getElementById("totalAmount");
  const $totalItems = document.getElementById("totalItems");
  const $btnProcess = document.getElementById("btnProcess");
  const $btnClear = document.getElementById("btnClearCart");
  const $feedback = document.getElementById("scanFeedback");
  const $supplierId = document.getElementById("supplierId");
  const $notes = document.getElementById("purchaseNotes");

  /* ── Event Binding ──────────────────────────── */
  $barcode.addEventListener("keydown", onBarcodeKey);
  $barcode.addEventListener("input", onSearchInput);
  $btnProcess.addEventListener("click", processPurchase);
  $btnClear.addEventListener("click", clearCart);
  document
    .getElementById("btnNewPurchase")
    .addEventListener("click", newPurchase);

  document.addEventListener("click", function (e) {
    if (!$barcode.contains(e.target) && !$dropdown.contains(e.target)) {
      $dropdown.classList.remove("show");
    }
  });

  /* ── Barcode Scanner ────────────────────────── */
  function onBarcodeKey(e) {
    if (e.key !== "Enter") return;
    e.preventDefault();
    var val = $barcode.value.trim();
    if (val) searchByBarcode(val);
  }

  function searchByBarcode(barcode) {
    feedback("loading", "Mencari produk...");
    var form = new FormData();
    form.append("barcode", barcode);
    form.append("context", "purchase");

    fetch(BASE_URL + "products/searchByBarcode", { method: "POST", body: form })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          addToCart(data.product);
          $barcode.value = "";
          $dropdown.classList.remove("show");
          feedback("success", "\u2713 " + data.product.name + " ditambahkan");
        } else {
          searchProducts(barcode);
          feedback(
            "warning",
            "Produk tidak ditemukan via barcode, mencari nama...",
          );
        }
      })
      .catch(function () {
        feedback("danger", "Gagal mencari produk");
      });
  }

  /* ── Product Search ─────────────────────────── */
  function onSearchInput() {
    clearTimeout(searchTimeout);
    var val = $barcode.value.trim();
    if (val.length < 2) {
      $dropdown.classList.remove("show");
      return;
    }
    searchTimeout = setTimeout(function () {
      searchProducts(val);
    }, 300);
  }

  function searchProducts(keyword) {
    fetch(BASE_URL + "products/search?q=" + encodeURIComponent(keyword))
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success && data.products.length > 0) {
          renderDropdown(data.products);
        } else {
          $dropdown.innerHTML =
            '<div class="dropdown-item text-muted py-2">Produk tidak ditemukan</div>';
          $dropdown.classList.add("show");
        }
      })
      .catch(function () {
        $dropdown.classList.remove("show");
      });
  }

  function renderDropdown(products) {
    $dropdown.innerHTML = products
      .map(function (p) {
        return (
          '<button type="button" class="dropdown-item d-flex justify-content-between align-items-center py-2" ' +
          "data-product='" +
          JSON.stringify(p).replace(/'/g, "&#39;") +
          "'>" +
          '<div><div class="fw-semibold">' +
          esc(p.name) +
          "</div>" +
          '<small class="text-muted">' +
          esc(p.barcode) +
          " &middot; " +
          esc(p.category || "-") +
          "</small></div>" +
          '<div class="text-end"><div class="fw-semibold">' +
          rp(p.price) +
          "</div>" +
          '<small class="text-muted">Stok: ' +
          p.stock +
          "</small></div></button>"
        );
      })
      .join("");

    $dropdown.querySelectorAll(".dropdown-item").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var product = JSON.parse(this.dataset.product);
        addToCart(product);
        $barcode.value = "";
        $dropdown.classList.remove("show");
        feedback("success", "\u2713 " + product.name + " ditambahkan");
        $barcode.focus();
      });
    });

    $dropdown.classList.add("show");
  }

  /* ── Cart Management ────────────────────────── */
  function addToCart(product) {
    var existing = cart.find(function (i) {
      return i.id === product.id;
    });

    if (existing) {
      existing.quantity++;
      existing.subtotal = existing.quantity * existing.price;
    } else {
      cart.push({
        id: product.id,
        barcode: product.barcode,
        name: product.name,
        price: product.price, // Default purchase price = sell price
        stock: product.stock,
        quantity: 1,
        subtotal: product.price,
      });
    }

    renderCart();
    updateTotals();
  }

  function renderCart() {
    if (cart.length === 0) {
      $cartBody.innerHTML = "";
      $cartBody.appendChild($emptyRow);
      $emptyRow.classList.remove("d-none");
      $btnClear.classList.add("d-none");
      return;
    }

    $btnClear.classList.remove("d-none");

    $cartBody.innerHTML = cart
      .map(function (item, idx) {
        return (
          "<tr>" +
          "<td>" +
          (idx + 1) +
          "</td>" +
          '<td><div class="fw-semibold">' +
          esc(item.name) +
          "</div>" +
          '<small class="text-muted"><code>' +
          esc(item.barcode) +
          "</code></small></td>" +
          '<td><div class="input-group input-group-sm" style="width:130px">' +
          '<span class="input-group-text">Rp</span>' +
          '<input type="number" class="form-control text-end price-input" value="' +
          item.price +
          '" min="1" data-id="' +
          item.id +
          '" />' +
          "</div></td>" +
          '<td><div class="input-group input-group-sm" style="width:120px">' +
          '<button class="btn btn-outline-secondary" type="button" data-act="dec" data-id="' +
          item.id +
          '"><i class="bx bx-minus"></i></button>' +
          '<input type="number" class="form-control text-center qty-input" value="' +
          item.quantity +
          '" min="1" data-id="' +
          item.id +
          '" />' +
          '<button class="btn btn-outline-secondary" type="button" data-act="inc" data-id="' +
          item.id +
          '"><i class="bx bx-plus"></i></button>' +
          "</div></td>" +
          '<td class="fw-semibold">' +
          rp(item.subtotal) +
          "</td>" +
          '<td><button class="btn btn-sm btn-icon btn-outline-danger" data-act="del" data-id="' +
          item.id +
          '">' +
          '<i class="bx bx-trash"></i></button></td></tr>'
        );
      })
      .join("");

    $cartBody.querySelectorAll("[data-act]").forEach(function (btn) {
      btn.addEventListener("click", onCartAction);
    });
    $cartBody.querySelectorAll(".qty-input").forEach(function (inp) {
      inp.addEventListener("change", onQtyChange);
    });
    $cartBody.querySelectorAll(".price-input").forEach(function (inp) {
      inp.addEventListener("change", onPriceChange);
    });
  }

  function onCartAction(e) {
    var btn = e.currentTarget;
    var act = btn.dataset.act;
    var id = parseInt(btn.dataset.id);
    var item = cart.find(function (i) {
      return i.id === id;
    });
    if (!item) return;

    if (act === "inc") {
      item.quantity++;
      item.subtotal = item.quantity * item.price;
    } else if (act === "dec") {
      if (item.quantity > 1) {
        item.quantity--;
        item.subtotal = item.quantity * item.price;
      } else {
        cart = cart.filter(function (i) {
          return i.id !== id;
        });
      }
    } else if (act === "del") {
      cart = cart.filter(function (i) {
        return i.id !== id;
      });
    }

    renderCart();
    updateTotals();
  }

  function onQtyChange(e) {
    var id = parseInt(e.target.dataset.id);
    var item = cart.find(function (i) {
      return i.id === id;
    });
    if (!item) return;

    var qty = parseInt(e.target.value) || 1;
    qty = Math.max(1, qty);
    item.quantity = qty;
    item.subtotal = qty * item.price;

    renderCart();
    updateTotals();
  }

  function onPriceChange(e) {
    var id = parseInt(e.target.dataset.id);
    var item = cart.find(function (i) {
      return i.id === id;
    });
    if (!item) return;

    var price = parseInt(e.target.value) || 1;
    price = Math.max(1, price);
    item.price = price;
    item.subtotal = item.quantity * price;

    renderCart();
    updateTotals();
  }

  /* ── Totals ─────────────────────────────────── */
  function updateTotals() {
    var total = cart.reduce(function (s, i) {
      return s + i.subtotal;
    }, 0);
    var count = cart.reduce(function (s, i) {
      return s + i.quantity;
    }, 0);

    $totalAmount.textContent = rp(total);
    $totalItems.textContent = count;
    $btnProcess.disabled = cart.length === 0;
  }

  /* ── Process Purchase ───────────────────────── */
  function processPurchase() {
    if (cart.length === 0) return;

    $btnProcess.disabled = true;
    $btnProcess.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';

    fetch(BASE_URL + "purchases/store", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        items: cart.map(function (item) {
          return {
            product_id: item.id,
            quantity: item.quantity,
            price: item.price,
          };
        }),
        supplier_id: $supplierId.value || null,
        notes: $notes.value.trim(),
      }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          showSuccess(data);
        } else {
          feedback("danger", data.message || "Gagal menyimpan pembelian");
        }
      })
      .catch(function () {
        feedback("danger", "Terjadi kesalahan jaringan");
      })
      .finally(function () {
        $btnProcess.disabled = false;
        $btnProcess.innerHTML =
          '<i class="bx bx-check-circle me-1"></i> Simpan Pembelian';
      });
  }

  function showSuccess(data) {
    document.getElementById("resultCode").textContent = data.purchase_code;
    document.getElementById("resultTotal").textContent = rp(data.total);
    document.getElementById("btnDetail").href =
      BASE_URL + "purchases/detail/" + data.purchase_id;

    var modal = new bootstrap.Modal(document.getElementById("successModal"));
    modal.show();
  }

  function newPurchase() {
    var modal = bootstrap.Modal.getInstance(
      document.getElementById("successModal"),
    );
    if (modal) modal.hide();

    cart = [];
    renderCart();
    updateTotals();
    $barcode.value = "";
    $supplierId.value = "";
    $notes.value = "";
    $barcode.focus();
    $feedback.innerHTML = "";
  }

  function clearCart() {
    if (!confirm("Kosongkan daftar barang?")) return;
    cart = [];
    renderCart();
    updateTotals();
    $barcode.focus();
  }

  /* ── Utilities ──────────────────────────────── */
  function rp(n) {
    var abs = Math.abs(n);
    var formatted = abs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return (n < 0 ? "-Rp " : "Rp ") + formatted;
  }

  function esc(str) {
    var d = document.createElement("div");
    d.textContent = str || "";
    return d.innerHTML;
  }

  function feedback(type, msg) {
    var cls = {
      success: "text-success",
      warning: "text-warning",
      danger: "text-danger",
      loading: "text-muted",
    };
    $feedback.className = "mt-2 small " + (cls[type] || "");
    $feedback.textContent = msg;
    if (type !== "loading")
      setTimeout(function () {
        $feedback.textContent = "";
      }, 3000);
  }
})();

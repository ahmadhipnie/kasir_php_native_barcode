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

  /* ── Camera Barcode Scanner (QuaggaJS) ─────────────────── */
  let isScanning = false;
  let lastDetectedCode = null;
  let detectionCount = 0;
  let resetTimeout = null;
  const $btnCamera = document.getElementById("btnCamera");
  const $btnCloseCamera = document.getElementById("btnCloseCamera");
  const $cameraScanner = document.getElementById("cameraScanner");

  if ($btnCamera) {
    $btnCamera.addEventListener("click", startCamera);
  }

  if ($btnCloseCamera) {
    $btnCloseCamera.addEventListener("click", stopCamera);
  }

  function startCamera() {
    $cameraScanner.classList.remove("d-none");
    $btnCamera.disabled = true;

    // Enumerate available cameras
    Quagga.CameraAccess.enumerateVideoDevices()
      .then(function (cameras) {
        if (cameras && cameras.length) {
          // Filter out virtual cameras (OBS, Snap, ManyCam, etc.)
          var realCameras = cameras.filter(function (cam) {
            var label = (cam.label || "").toLowerCase();
            return (
              !label.includes("obs") &&
              !label.includes("virtual") &&
              !label.includes("snap camera") &&
              !label.includes("manycam") &&
              !label.includes("snap cam")
            );
          });

          // Use filtered cameras if available, otherwise use all
          var camerasToUse = realCameras.length > 0 ? realCameras : cameras;

          // Show camera selector if multiple cameras
          if (camerasToUse.length > 1) {
            showCameraSelector(camerasToUse);
          } else {
            // Only one camera, use it directly
            initScanner(camerasToUse[0].deviceId);
          }
        } else {
          feedback("danger", "Tidak ada kamera yang tersedia");
          stopCamera();
        }
      })
      .catch(function (err) {
        console.error("Camera enumeration error:", err);
        feedback("danger", "Gagal mengakses kamera: " + err.message);
        stopCamera();
      });
  }

  function showCameraSelector(cameras) {
    var readerDiv = document.getElementById("reader");
    var html =
      '<div class="p-3"><label class="form-label fw-semibold">Pilih Kamera:</label>';
    html += '<select class="form-select mb-3" id="cameraSelect">';

    cameras.forEach(function (cam, idx) {
      var label = cam.label || "Kamera " + (idx + 1);
      // Mark external/USB cameras
      if (
        label.toLowerCase().includes("usb") ||
        label.toLowerCase().includes("external")
      ) {
        label = "📷 " + label + " (Eksternal)";
      }
      html += '<option value="' + cam.deviceId + '">' + label + "</option>";
    });

    html += "</select>";
    html +=
      '<button type="button" class="btn btn-success w-100" id="btnStartScan"><i class="bx bx-camera me-1"></i> Mulai Scan</button></div>';

    readerDiv.innerHTML = html;

    document
      .getElementById("btnStartScan")
      .addEventListener("click", function () {
        var selectedCameraId = document.getElementById("cameraSelect").value;
        initScanner(selectedCameraId);
      });
  }

  function initScanner(deviceId) {
    var readerDiv = document.getElementById("reader");
    readerDiv.innerHTML =
      '<div id="barcode-scanner"></div>' +
      '<div class="scanner-overlay">' +
      '  <div class="scanner-frame">' +
      '    <div class="corner top-left"></div>' +
      '    <div class="corner top-right"></div>' +
      '    <div class="corner bottom-left"></div>' +
      '    <div class="corner bottom-right"></div>' +
      '    <div class="scan-line"></div>' +
      "  </div>" +
      "</div>" +
      '<div class="text-center mt-2 p-2 bg-light"><small class="text-success fw-semibold"><i class="bx bx-bullseye me-1"></i>Letakkan barcode di dalam kotak merah (jarak 15-20cm)</small></div>';

    // Wait for DOM to be ready
    setTimeout(function () {
      var scannerTarget = document.getElementById("barcode-scanner");

      if (!scannerTarget) {
        console.error("Scanner target not found");
        feedback("danger", "Gagal membuat area scanner");
        stopCamera();
        return;
      }

      Quagga.init(
        {
          inputStream: {
            name: "Live",
            type: "LiveStream",
            target: scannerTarget,
            constraints: {
              width: { min: 640, ideal: 1280, max: 1920 },
              height: { min: 480, ideal: 720, max: 1080 },
              facingMode: "environment",
              deviceId: deviceId,
            },
            area: {
              top: "25%",
              right: "15%",
              left: "15%",
              bottom: "25%",
            },
          },
          decoder: {
            readers: ["ean_reader", "ean_8_reader", "code_128_reader"],
            debug: {
              drawBoundingBox: false,
              showFrequency: false,
              drawScanline: false,
              showPattern: false,
            },
            multiple: false,
          },
          locator: {
            patchSize: "large",
            halfSample: false,
          },
          numOfWorkers: 2,
          frequency: 5,
          locate: true,
        },
        function (err) {
          if (err) {
            console.error("Quagga initialization error:", err);
            feedback(
              "danger",
              "Gagal menginisialisasi scanner: " + err.message,
            );
            stopCamera();
            return;
          }

          console.log("Quagga initialized successfully");
          Quagga.start();
          isScanning = true;
        },
      );

      // Handle successful barcode detection
      Quagga.onDetected(function (result) {
        if (!result || !result.codeResult || !result.codeResult.code) return;

        var code = result.codeResult.code;
        var confidence =
          result.codeResult.decodedCodes.reduce(function (sum, code) {
            return sum + (code.error || 0);
          }, 0) / result.codeResult.decodedCodes.length;

        // Convert error rate to confidence percentage
        var confidencePercent = Math.max(0, (1 - confidence) * 100);

        console.log(
          "Barcode detected:",
          code,
          "Confidence:",
          confidencePercent.toFixed(1) + "%",
          "Count:",
          detectionCount + 1,
        );

        // Reset detection if no scan for 2 seconds
        if (resetTimeout) clearTimeout(resetTimeout);
        resetTimeout = setTimeout(function () {
          lastDetectedCode = null;
          detectionCount = 0;
        }, 2000);

        // Only accept very high-confidence reads (>85%) and valid length
        if (code.length >= 4 && confidencePercent > 85) {
          // Require 2 consecutive identical reads for speed and reliability
          if (lastDetectedCode === code) {
            detectionCount++;
            console.log("🔹 Matching code, count:", detectionCount);
            if (detectionCount >= 2) {
              console.log(
                "✓ Barcode CONFIRMED:",
                code,
                "after",
                detectionCount,
                "consistent reads",
              );
              searchByBarcode(code);
              lastDetectedCode = null;
              detectionCount = 0;
              if (resetTimeout) clearTimeout(resetTimeout);
              stopCamera();
            }
          } else {
            console.log("🔄 New code detected, resetting count");
            lastDetectedCode = code;
            detectionCount = 1;
          }
        } else if (code.length >= 4) {
          console.log(
            "⚠ Low confidence (",
            confidencePercent.toFixed(1) + "%), ignoring...",
          );
        } else if (code) {
          console.log("⚠ Barcode too short (", code.length, "chars), minimum 4 required");
        }
      });
    }, 100);
  }

  function stopCamera() {
    if (isScanning) {
      Quagga.stop();
      isScanning = false;
    }

    var readerDiv = document.getElementById("reader");
    if (readerDiv) {
      readerDiv.innerHTML = "";
    }

    $cameraScanner.classList.add("d-none");
    $btnCamera.disabled = false;
  }
})();

/**
 * Product Barcode Camera Scanner using QuaggaJS
 * Professional barcode scanning optimized for product barcodes
 */
(function () {
  "use strict";

  let isScanning = false;
  let availableCameras = [];
  let lastDetectedCode = null;
  let detectionCount = 0;
  let resetTimeout = null;
  const $barcode = document.getElementById("barcode");
  const $btnCamera = document.getElementById("btnCamera");
  const $btnCloseCamera = document.getElementById("btnCloseCamera");
  const $cameraScanner = document.getElementById("cameraScanner");

  if (!$barcode || !$btnCamera || !$btnCloseCamera || !$cameraScanner) {
    return;
  }

  $btnCamera.addEventListener("click", startCamera);
  $btnCloseCamera.addEventListener("click", stopCamera);

  function startCamera() {
    $cameraScanner.classList.remove("d-none");
    $btnCamera.disabled = true;

    // Get available cameras
    Quagga.CameraAccess.enumerateVideoDevices()
      .then(function (devices) {
        availableCameras = devices.filter(function (device) {
          var label = (device.label || "").toLowerCase();
          return (
            !label.includes("obs") &&
            !label.includes("virtual") &&
            !label.includes("snap camera") &&
            !label.includes("manycam")
          );
        });

        if (availableCameras.length === 0) {
          availableCameras = devices;
        }

        if (availableCameras.length > 1) {
          showCameraSelector(availableCameras);
        } else if (availableCameras.length === 1) {
          initScanner(availableCameras[0].deviceId);
        } else {
          showFeedback("danger", "Tidak ada kamera yang tersedia");
          stopCamera();
        }
      })
      .catch(function (err) {
        console.error("Camera enumeration error:", err);
        showFeedback("danger", "Gagal mengakses kamera: " + err.message);
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
      if (
        label.toLowerCase().includes("usb") ||
        label.toLowerCase().includes("external") ||
        label.toLowerCase().includes("back")
      ) {
        label = "📷 " + label + " (Eksternal)";
      }
      html += '<option value="' + cam.deviceId + '">' + label + "</option>";
    });

    html += "</select>";
    html +=
      '<button type="button" class="btn btn-primary w-100" id="btnStartScan"><i class="bx bx-camera me-1"></i> Mulai Scan</button></div>';

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
      '<div class="text-center mt-2 p-2 bg-light"><small class="text-primary fw-semibold"><i class="bx bx-bullseye me-1"></i>Letakkan barcode di dalam kotak merah (jarak 15-20cm)</small></div>';

    // Wait for DOM to be ready
    setTimeout(function () {
      var scannerTarget = document.getElementById("barcode-scanner");

      if (!scannerTarget) {
        console.error("Scanner target not found");
        showFeedback("danger", "Gagal membuat area scanner");
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
              deviceId: deviceId,
              width: { min: 640, ideal: 1280, max: 1920 },
              height: { min: 480, ideal: 720, max: 1080 },
              facingMode: "environment",
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
            showFeedback(
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

      // On barcode detected
      Quagga.onDetected(function (result) {
        if (!isScanning) return;

        var code = result.codeResult.code;
        var confidence =
          result.codeResult.decodedCodes.reduce(function (sum, code) {
            return sum + (code.error || 0);
          }, 0) / result.codeResult.decodedCodes.length;

        // Convert error rate to confidence percentage (lower error = higher confidence)
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
        if (code && code.length >= 4 && confidencePercent > 85) {
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
              $barcode.value = code;
              $barcode.focus();
              showFeedback("success", "✓ Barcode berhasil discan: " + code);
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
        } else if (code && code.length >= 4) {
          console.log("⚠ Low confidence, ignoring...");
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
    $cameraScanner.classList.add("d-none");
    $btnCamera.disabled = false;

    var readerDiv = document.getElementById("reader");
    if (readerDiv) {
      readerDiv.innerHTML = "";
    }
  }

  function showFeedback(type, message) {
    var $feedback = document.getElementById("barcodeFeedback");

    if (!$feedback) {
      $feedback = document.createElement("div");
      $feedback.id = "barcodeFeedback";
      $feedback.className = "mt-2";
      $cameraScanner.parentNode.appendChild($feedback);
    }

    var alertClass = {
      success: "alert-success",
      danger: "alert-danger",
      warning: "alert-warning",
    };

    $feedback.className = "mt-2 alert " + (alertClass[type] || "alert-info");
    $feedback.textContent = message;

    setTimeout(function () {
      $feedback.className = "mt-2";
      $feedback.textContent = "";
    }, 3000);
  }

  // Cleanup on page unload
  window.addEventListener("beforeunload", function () {
    stopCamera();
  });
})();

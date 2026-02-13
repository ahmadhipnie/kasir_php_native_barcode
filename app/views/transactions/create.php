<?php ob_start(); ?>

<div class="row">
    <!-- Left: Scanner + Cart -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body pb-2">
                <div style="position:relative">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-barcode me-1"></i> Scan / Cari Produk
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" id="barcodeInput"
                            placeholder="Scan barcode atau ketik nama produk..." autofocus autocomplete="off" />
                        <button class="btn btn-outline-primary" id="btnCamera" type="button" title="Scan dengan Kamera">
                            <i class="bx bx-camera"></i>
                        </button>
                    </div>
                    <div class="dropdown-menu w-100 shadow" id="searchDropdown" style="max-height:280px;overflow-y:auto"></div>
                </div>
                <div id="scanFeedback" class="mt-2"></div>
                <!-- Camera Scanner -->
                <div id="cameraScanner" class="d-none mt-3">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span><i class="bx bx-camera me-1"></i> Scan Barcode dengan Kamera</span>
                            <button class="btn btn-sm btn-outline-light" id="btnCloseCamera" type="button">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="reader" style="width:100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bx bx-cart me-2"></i>Keranjang Belanja</h5>
                <button class="btn btn-outline-danger btn-sm d-none" id="btnClearCart">
                    <i class="bx bx-trash me-1"></i> Kosongkan
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="cartTable">
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Produk</th>
                            <th style="width:14%">Harga</th>
                            <th style="width:18%">Qty</th>
                            <th style="width:14%">Subtotal</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody">
                        <tr id="emptyRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bx bx-barcode" style="font-size:2.5rem"></i>
                                <div class="mt-2">Scan barcode atau cari produk untuk mulai transaksi</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Payment -->
    <div class="col-lg-4">
        <div class="card mb-4 sticky-top" style="top:80px;z-index:10">
            <div class="card-header bg-primary">
                <h5 class="mb-0 text-white"><i class="bx bx-receipt me-2"></i>Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Total Item</span>
                    <span id="totalItems" class="fw-semibold">0</span>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="h6 mb-0">Total</span>
                    <span id="totalAmount" class="h4 mb-0 text-primary fw-bold">Rp 0</span>
                </div>

                <div class="mb-3">
                    <label for="paymentAmount" class="form-label fw-semibold">Jumlah Bayar</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="paymentAmount" placeholder="0" min="0" disabled />
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-3" id="quickPayButtons"></div>

                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
                    <span class="fw-semibold">Kembalian</span>
                    <span id="changeAmount" class="h5 mb-0 fw-bold">Rp 0</span>
                </div>

                <button class="btn btn-primary btn-lg w-100" id="btnProcess" disabled>
                    <i class="bx bx-check-circle me-1"></i> Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <span class="bx bx-check-circle text-success" style="font-size:5rem"></span>
                </div>
                <h4 class="mb-2">Transaksi Berhasil!</h4>
                <p class="text-muted mb-1">Kode: <strong id="resultCode"></strong></p>
                <p class="text-muted mb-1">Total: <strong id="resultTotal"></strong></p>
                <p class="text-muted mb-1">Bayar: <strong id="resultPayment"></strong></p>
                <p class="mb-4">Kembalian: <strong id="resultChange" class="text-success h5"></strong></p>
                <div class="d-flex gap-2 justify-content-center">
                    <a id="btnReceipt" href="#" class="btn btn-outline-primary">
                        <i class="bx bx-printer me-1"></i> Lihat Struk
                    </a>
                    <button class="btn btn-primary" id="btnNewTransaction">
                        <i class="bx bx-plus me-1"></i> Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageScripts = '
<style>
#barcode-scanner {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    background: #000;
}
#barcode-scanner video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
}
#barcode-scanner canvas {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 2 !important;
}
#reader {
    max-width: 100%;
    position: relative;
}
.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    z-index: 1000;
}
.scanner-frame {
    position: relative;
    width: 70%;
    height: 50%;
    border: 2px solid #dc3545;
    background: transparent;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
}
.scanner-frame .corner {
    position: absolute;
    width: 30px;
    height: 30px;
    border: 3px solid #dc3545;
}
.scanner-frame .corner.top-left {
    top: -2px;
    left: -2px;
    border-right: none;
    border-bottom: none;
}
.scanner-frame .corner.top-right {
    top: -2px;
    right: -2px;
    border-left: none;
    border-bottom: none;
}
.scanner-frame .corner.bottom-left {
    bottom: -2px;
    left: -2px;
    border-right: none;
    border-top: none;
}
.scanner-frame .corner.bottom-right {
    bottom: -2px;
    right: -2px;
    border-left: none;
    border-top: none;
}
.scanner-frame .scan-line {
    position: absolute;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #dc3545, transparent);
    animation: scan 2s linear infinite;
}
@keyframes scan {
    0% { top: 0; }
    50% { top: calc(100% - 2px); }
    100% { top: 0; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js"></script>
<script src="' . BASE_URL . 'assets/js/transaction.js"></script>
';
include '../app/views/layouts/header.php';
?>
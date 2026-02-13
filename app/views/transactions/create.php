<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Scan Barcode atau Pilih Produk</h5>
                <div class="mb-3">
                    <input type="text" class="form-control form-control-lg" id="barcodeInput" placeholder="Scan barcode di sini..." autofocus>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Keranjang Belanja</h5>
                <div class="table-responsive">
                    <table class="table" id="cartTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Keranjang kosong</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-body">
                <h5 class="card-title">Total Pembayaran</h5>
                <h2 id="totalAmount">Rp 0</h2>
                <hr>
                <div class="mb-3">
                    <label for="paymentAmount" class="form-label">Jumlah Bayar</label>
                    <input type="number" class="form-control" id="paymentAmount" placeholder="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kembalian</label>
                    <h4 id="changeAmount">Rp 0</h4>
                </div>
                <button class="btn btn-primary btn-lg w-100" id="processPayment">Proses Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/transaction.js"></script>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

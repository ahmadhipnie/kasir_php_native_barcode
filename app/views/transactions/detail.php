<?php ob_start(); ?>

<?php if ($transaction): ?>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Informasi Transaksi</h5>
            <div>
                <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                    <i class="bx bx-printer me-1"></i> Cetak Struk
                </button>
                <a href="<?= BASE_URL ?>transactions" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label text-muted">Kode Transaksi</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext fw-semibold"><?= htmlspecialchars($transaction->transaction_code) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label text-muted">Tanggal</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext"><?= date('d/m/Y H:i:s', strtotime($transaction->transaction_date)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Detail Produk</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php foreach ($transaction->items as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item->product_name) ?></strong>
                                <br><small class="text-muted"><code><?= htmlspecialchars($item->barcode) ?></code></small>
                            </td>
                            <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                            <td><span class="badge bg-label-primary"><?= $item->quantity ?></span></td>
                            <td class="text-end fw-semibold">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-semibold border-top">Total</td>
                        <td class="text-end fw-bold border-top fs-5">Rp <?= number_format($transaction->total_amount, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-muted">Bayar</td>
                        <td class="text-end">Rp <?= number_format($transaction->payment_amount, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end text-muted">Kembalian</td>
                        <td class="text-end text-success fw-semibold">Rp <?= number_format($transaction->change_amount, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="bx bx-error me-2"></i> Transaksi tidak ditemukan
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

/* Print-only receipt styles */
$pageStyles = '
<style>
@media print {
    .layout-navbar, .layout-menu, .layout-footer,
    .card-header .btn, .content-footer { display: none !important; }
    .layout-page { margin: 0 !important; padding: 0 !important; }
    .content-wrapper { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>';

include '../app/views/layouts/header.php';
?>
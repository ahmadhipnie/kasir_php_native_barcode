<?php ob_start(); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><?= $title ?></h5>
                <a href="<?= BASE_URL ?>purchases" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Barcode</th>
                            <th>Harga Beli</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchase->items as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($item->product_name) ?></strong></td>
                                <td><code><?= htmlspecialchars($item->barcode) ?></code></td>
                                <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                                <td><?= $item->quantity ?></td>
                                <td class="fw-semibold">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total</td>
                            <td class="fw-bold text-success">Rp <?= number_format($purchase->total_amount, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-success">
                <h5 class="mb-0 text-white"><i class="bx bx-info-circle me-2"></i>Info Pembelian</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Kode Pembelian</small>
                    <strong><?= htmlspecialchars($purchase->purchase_code) ?></strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal</small>
                    <strong><?= date('d/m/Y H:i', strtotime($purchase->purchase_date ?? $purchase->created_at)) ?></strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Supplier</small>
                    <strong><?= htmlspecialchars($purchase->supplier_name ?? '-') ?></strong>
                </div>
                <?php if (!empty($purchase->notes)): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Catatan</small>
                        <span><?= htmlspecialchars($purchase->notes) ?></span>
                    </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h6 mb-0">Total</span>
                    <span class="h4 mb-0 text-success fw-bold">Rp <?= number_format($purchase->total_amount, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
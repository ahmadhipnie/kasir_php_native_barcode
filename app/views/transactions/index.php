<?php ob_start(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><?= $title ?></h5>
        <a href="<?= BASE_URL ?>transactions/create" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Transaksi Baru
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                    <th style="width:5%">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $i => $trx): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($trx->transaction_code) ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($trx->transaction_date)) ?></td>
                            <td class="fw-semibold">Rp <?= number_format($trx->total_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($trx->payment_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($trx->change_amount, 0, ',', '.') ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>transactions/detail/<?= $trx->id ?>"
                                    class="btn btn-sm btn-icon btn-outline-primary" title="Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bx bx-receipt" style="font-size:2.5rem"></i>
                            <div class="mt-2">Belum ada transaksi</div>
                            <a href="<?= BASE_URL ?>transactions/create" class="btn btn-primary btn-sm mt-3">
                                <i class="bx bx-plus me-1"></i> Buat Transaksi
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
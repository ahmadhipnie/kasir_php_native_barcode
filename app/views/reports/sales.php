<?php ob_start(); ?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-filter-alt me-2"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>reports/sales" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>" />
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>" />
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bx bx-search me-1"></i> Tampilkan
                </button>
                <a href="<?= BASE_URL ?>reports/exportSales?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-success me-2">
                    <i class="bx bx-file me-1"></i> Export Excel
                </a>
                <a href="<?= BASE_URL ?>reports/printSales?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-outline-secondary" target="_blank">
                    <i class="bx bx-printer me-1"></i> Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Total Penjualan</small>
                        <h4 class="mb-0 text-primary">Rp <?= number_format($totalSales, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar bg-label-primary">
                        <span class="avatar-initial rounded"><i class="bx bx-trending-up"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Jumlah Transaksi</small>
                        <h4 class="mb-0"><?= count($transactions) ?></h4>
                    </div>
                    <div class="avatar bg-label-info">
                        <span class="avatar-initial rounded"><i class="bx bx-receipt"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Periode</small>
                        <h6 class="mb-0"><?= date('d/m/Y', strtotime($from)) ?> - <?= date('d/m/Y', strtotime($to)) ?></h6>
                    </div>
                    <div class="avatar bg-label-warning">
                        <span class="avatar-initial rounded"><i class="bx bx-calendar"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>transactions/detail/<?= $t->id ?>">
                                    <code><?= htmlspecialchars($t->transaction_code) ?></code>
                                </a>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($t->transaction_date)) ?></td>
                            <td class="fw-semibold">Rp <?= number_format($t->total_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($t->payment_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($t->change_amount, 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($t->cashier_name ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($transactions)): ?>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total</td>
                        <td class="text-primary">Rp <?= number_format($totalSales, 0, ',', '.') ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
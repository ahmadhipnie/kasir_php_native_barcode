<?php ob_start(); ?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-filter-alt me-2"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>reports/purchases" class="row g-3 align-items-end">
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
                <a href="<?= BASE_URL ?>reports/exportPurchases?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-success me-2">
                    <i class="bx bx-file me-1"></i> Export Excel
                </a>
                <a href="<?= BASE_URL ?>reports/printPurchases?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-outline-secondary" target="_blank">
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
                        <small class="text-muted d-block">Total Pembelian</small>
                        <h4 class="mb-0 text-success">Rp <?= number_format($totalPurchases, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar bg-label-success">
                        <span class="avatar-initial rounded"><i class="bx bx-store"></i></span>
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
                        <small class="text-muted d-block">Jumlah Pembelian</small>
                        <h4 class="mb-0"><?= count($purchases) ?></h4>
                    </div>
                    <div class="avatar bg-label-info">
                        <span class="avatar-initial rounded"><i class="bx bx-purchase-tag"></i></span>
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
                    <th>Kode Pembelian</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($purchases)): ?>
                    <?php foreach ($purchases as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>purchases/detail/<?= $p->id ?>">
                                    <code><?= htmlspecialchars($p->purchase_code) ?></code>
                                </a>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($p->purchase_date ?? $p->created_at)) ?></td>
                            <td><?= htmlspecialchars($p->supplier_name ?? '-') ?></td>
                            <td class="fw-semibold">Rp <?= number_format($p->total_amount, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada pembelian pada periode ini</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($purchases)): ?>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">Total</td>
                        <td class="text-success">Rp <?= number_format($totalPurchases, 0, ',', '.') ?></td>
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
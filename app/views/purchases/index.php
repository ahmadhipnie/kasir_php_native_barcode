<?php ob_start(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><?= $title ?></h5>
        <a href="<?= BASE_URL ?>purchases/create" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Pembelian Baru
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Pembelian</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($purchases)): ?>
                    <?php foreach ($purchases as $i => $purchase): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($purchase->purchase_code) ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($purchase->purchase_date ?? $purchase->created_at)) ?></td>
                            <td><?= htmlspecialchars($purchase->supplier_name ?? '-') ?></td>
                            <td class="fw-semibold">Rp <?= number_format($purchase->total_amount, 0, ',', '.') ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>purchases/detail/<?= $purchase->id ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bx bx-store bx-lg"></i>
                            <p class="mt-2 mb-0">Belum ada riwayat pembelian. <a href="<?= BASE_URL ?>purchases/create">Buat pembelian baru</a></p>
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
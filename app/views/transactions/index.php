<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= $transaction->transaction_code ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($transaction->transaction_date)) ?></td>
                        <td>Rp <?= number_format($transaction->total_amount, 0, ',', '.') ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>transactions/detail/<?= $transaction->id ?>" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Belum ada transaksi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

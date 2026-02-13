<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Penjualan Hari Ini</h5>
                <h3>Rp <?= number_format($todaySales ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Transaksi Hari Ini</h5>
                <h3><?= $todayTransactions ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>Transaksi Terakhir</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentTransactions)): ?>
                        <?php foreach ($recentTransactions as $transaction): ?>
                            <tr>
                                <td><?= $transaction->transaction_code ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($transaction->transaction_date)) ?></td>
                                <td>Rp <?= number_format($transaction->total_amount, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada transaksi</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

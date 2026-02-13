<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
    <a href="<?= BASE_URL ?>transactions" class="btn btn-secondary">Kembali</a>
</div>

<?php if ($transaction): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Informasi Transaksi</h5>
            <table class="table table-borderless">
                <tr>
                    <td width="200">Kode Transaksi</td>
                    <td>: <?= $transaction->transaction_code ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= date('d/m/Y H:i', strtotime($transaction->transaction_date)) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Detail Produk</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaction->items as $item): ?>
                        <tr>
                            <td><?= $item->product_name ?> (<?= $item->barcode ?>)</td>
                            <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                            <td><?= $item->quantity ?></td>
                            <td>Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td><strong>Rp <?= number_format($transaction->total_amount, 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Bayar</td>
                        <td>Rp <?= number_format($transaction->payment_amount, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Kembalian</td>
                        <td>Rp <?= number_format($transaction->change_amount, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">Transaksi tidak ditemukan</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

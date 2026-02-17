<?php ob_start(); ?>

<!-- Stats cards row -->
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-cart"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Penjualan Hari Ini</span>
                <h3 class="card-title mb-2">Rp <?= number_format($todaySales, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-receipt"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Transaksi Hari Ini</span>
                <h3 class="card-title mb-2"><?= $todayTransactions ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-wallet"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Penjualan Bulan Ini</span>
                <h3 class="card-title mb-2">Rp <?= number_format($monthlyTotal, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-store"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Pembelian Bulan Ini</span>
                <h3 class="card-title mb-2">Rp <?= number_format($monthlyPurchases ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sales Chart -->
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Penjualan 7 Hari Terakhir</h5>
                </div>
            </div>
            <div class="card-body px-2">
                <div id="salesChart"></div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-lg-4 mb-4 order-1">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Stok Menipis</h5>
                    <small class="text-muted">&le; 10 item tersisa</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($lowStockProducts)): ?>
                    <ul class="p-0 m-0">
                        <?php foreach (array_slice($lowStockProducts, 0, 6) as $product): ?>
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-<?= $product->stock <= 3 ? 'danger' : 'warning' ?>">
                                        <i class="bx bx-package"></i>
                                    </span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0"><?= htmlspecialchars($product->name) ?></h6>
                                        <small class="text-muted"><?= $product->barcode ?></small>
                                    </div>
                                    <div class="user-progress">
                                        <small class="fw-semibold text-<?= $product->stock <= 3 ? 'danger' : 'warning' ?>">
                                            <?= $product->stock ?> pcs
                                        </small>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-check-circle bx-lg text-success"></i>
                        <p class="mt-2 mb-0">Semua stok aman</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Transaksi Terakhir</h5>
        <a href="<?= BASE_URL ?>transactions" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($recentTransactions)): ?>
                    <?php foreach ($recentTransactions as $trx): ?>
                        <tr>
                            <td>
                                <a href="<?= BASE_URL ?>transactions/detail/<?= $trx->id ?>">
                                    <strong><?= $trx->transaction_code ?></strong>
                                </a>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($trx->transaction_date)) ?></td>
                            <td>Rp <?= number_format($trx->total_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($trx->payment_amount, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($trx->change_amount, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

// Chart data — fill missing days so chart is continuous
$salesByDate = [];
foreach ($dailySales as $row) {
    $salesByDate[$row->date] = (int)$row->total;
}
$chartLabels = [];
$chartData   = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartLabels[] = date('d M', strtotime($date));
    $chartData[]   = $salesByDate[$date] ?? 0;
}

$pageStyles = '<link rel="stylesheet" href="' . BASE_URL . 'assets/vendor/libs/apex-charts/apex-charts.css" />';

$pageScripts = '
<script src="' . BASE_URL . 'assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var options = {
        series: [{ name: "Penjualan", data: ' . json_encode($chartData) . ' }],
        chart: { height: 300, type: "area", toolbar: { show: false },
            dropShadow: { enabled: true, top: 10, left: 0, blur: 3, opacity: 0.1 }
        },
        dataLabels: { enabled: false },
        stroke: { width: 3, curve: "smooth" },
        colors: ["#696cff"],
        fill: {
            type: "gradient",
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 }
        },
        xaxis: { categories: ' . json_encode($chartLabels) . ' },
        yaxis: {
            labels: {
                formatter: function(v) {
                    return "Rp " + new Intl.NumberFormat("id-ID").format(v);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(v) {
                    return "Rp " + new Intl.NumberFormat("id-ID").format(v);
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#salesChart"), options).render();
});
</script>';

include '../app/views/layouts/header.php';
?>  
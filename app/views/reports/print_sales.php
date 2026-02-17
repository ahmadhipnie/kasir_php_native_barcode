<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - <?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }

        .period {
            text-align: center;
            margin-bottom: 15px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background: #f8f8f8;
        }

        .summary {
            margin-top: 15px;
        }

        .summary p {
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #999;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                margin: 15mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h1><?= APP_NAME ?></h1>
        <h2>Laporan Penjualan</h2>
    </div>
    <div class="period">
        Periode: <?= date('d/m/Y', strtotime($from)) ?> s/d <?= date('d/m/Y', strtotime($to)) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:5%">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th class="text-right">Total</th>
                <th class="text-right">Bayar</th>
                <th class="text-right">Kembalian</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $i => $t): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($t->transaction_code) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t->transaction_date)) ?></td>
                        <td class="text-right">Rp <?= number_format($t->total_amount, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($t->payment_amount, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($t->change_amount, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($t->cashier_name ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total Penjualan</td>
                    <td class="text-right">Rp <?= number_format($totalSales, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <p>Total Transaksi: <strong><?= count($transactions) ?></strong></p>
        <p>Total Penjualan: <strong>Rp <?= number_format($totalSales, 0, ',', '.') ?></strong></p>
    </div>

    <div class="footer">
        Dicetak pada <?= date('d/m/Y H:i:s') ?> &mdash; <?= APP_NAME ?> v<?= APP_VERSION ?>
    </div>
</body>

</html>
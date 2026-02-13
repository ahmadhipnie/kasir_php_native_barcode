<?php

class Reports extends Controller
{
    private $transactionModel;
    private $purchaseModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->transactionModel = $this->model('Transaction');
        $this->purchaseModel    = $this->model('Purchase');
    }

    public function index()
    {
        $this->sales();
    }

    /** Sales report */
    public function sales()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');

        $transactions = $this->transactionModel->getByDateRange($from, $to);

        $totalSales = 0;
        foreach ($transactions as $t) $totalSales += (int)$t->total_amount;

        $this->view('reports/sales', [
            'title'        => 'Laporan Penjualan',
            'transactions' => $transactions,
            'totalSales'   => $totalSales,
            'from'         => $from,
            'to'           => $to
        ]);
    }

    /** Purchase report */
    public function purchases()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');

        $purchases = $this->purchaseModel->getByDateRange($from, $to);

        $totalPurchases = 0;
        foreach ($purchases as $p) $totalPurchases += (int)$p->total_amount;

        $this->view('reports/purchases', [
            'title'          => 'Laporan Pembelian',
            'purchases'      => $purchases,
            'totalPurchases' => $totalPurchases,
            'from'           => $from,
            'to'             => $to
        ]);
    }

    /** Export sales to CSV */
    public function exportSales()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $transactions = $this->transactionModel->getByDateRange($from, $to);

        $filename = "laporan_penjualan_{$from}_sd_{$to}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
        fputcsv($out, ['No', 'Kode Transaksi', 'Tanggal', 'Total', 'Bayar', 'Kembalian', 'Kasir'], ';');

        $no = 1;
        foreach ($transactions as $t) {
            fputcsv($out, [
                $no++,
                $t->transaction_code,
                date('d/m/Y H:i', strtotime($t->transaction_date)),
                (int)$t->total_amount,
                (int)$t->payment_amount,
                (int)$t->change_amount,
                $t->cashier_name ?? '-'
            ], ';');
        }
        fclose($out);
        exit;
    }

    /** Export purchases to CSV */
    public function exportPurchases()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $purchases = $this->purchaseModel->getByDateRange($from, $to);

        $filename = "laporan_pembelian_{$from}_sd_{$to}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['No', 'Kode Pembelian', 'Tanggal', 'Supplier', 'Total'], ';');

        $no = 1;
        foreach ($purchases as $p) {
            fputcsv($out, [
                $no++,
                $p->purchase_code,
                date('d/m/Y H:i', strtotime($p->purchase_date)),
                $p->supplier_name,
                (int)$p->total_amount
            ], ';');
        }
        fclose($out);
        exit;
    }

    /** Print-friendly sales report */
    public function printSales()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');

        $transactions = $this->transactionModel->getByDateRange($from, $to);

        $totalSales = 0;
        foreach ($transactions as $t) $totalSales += (int)$t->total_amount;

        $this->view('reports/print_sales', [
            'title'        => 'Cetak Laporan Penjualan',
            'transactions' => $transactions,
            'totalSales'   => $totalSales,
            'from'         => $from,
            'to'           => $to
        ]);
    }

    /** Print-friendly purchase report */
    public function printPurchases()
    {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $purchases = $this->purchaseModel->getByDateRange($from, $to);

        $totalPurchases = 0;
        foreach ($purchases as $p) $totalPurchases += (int)$p->total_amount;

        $this->view('reports/print_purchases', [
            'title'          => 'Cetak Laporan Pembelian',
            'purchases'      => $purchases,
            'totalPurchases' => $totalPurchases,
            'from'           => $from,
            'to'             => $to
        ]);
    }
}

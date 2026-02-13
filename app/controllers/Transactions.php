<?php

class Transactions extends Controller
{
    private $transactionModel;
    private $productModel;

    public function __construct()
    {
        $this->transactionModel = $this->model('Transaction');
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        $data = [
            'title' => 'Riwayat Transaksi',
            'transactions' => $this->transactionModel->all()
        ];

        $this->view('transactions/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Transaksi Baru',
            'products' => $this->productModel->all()
        ];

        $this->view('transactions/create', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $items = json_decode($_POST['items'] ?? '[]', true);
            $total = $_POST['total'] ?? 0;
            $payment = $_POST['payment'] ?? 0;
            $change = $_POST['change'] ?? 0;

            $transactionData = [
                'transaction_code' => $this->generateTransactionCode(),
                'total_amount' => $total,
                'payment_amount' => $payment,
                'change_amount' => $change,
                'transaction_date' => date('Y-m-d H:i:s')
            ];

            $transactionId = $this->transactionModel->createWithItems($transactionData, $items);

            if ($transactionId) {
                $this->jsonResponse([
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'transaction_code' => $transactionData['transaction_code']
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Transaksi gagal'], 400);
            }
        }
    }

    public function detail($id)
    {
        $data = [
            'title' => 'Detail Transaksi',
            'transaction' => $this->transactionModel->getWithItems($id)
        ];

        $this->view('transactions/detail', $data);
    }

    private function generateTransactionCode()
    {
        return 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

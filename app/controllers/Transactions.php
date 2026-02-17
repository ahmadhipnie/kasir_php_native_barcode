<?php

class Transactions extends Controller
{
    private $transactionModel;
    private $productModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->transactionModel = $this->model('Transaction');
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        $this->view('transactions/index', [
            'title'        => 'Riwayat Transaksi',
            'transactions' => $this->transactionModel->getRecent(50)
        ]);
    }

    /** POS page */
    public function create()
    {
        $this->view('transactions/create', [
            'title' => 'Transaksi Baru'
        ]);
    }

    /** API: Process transaction (called via AJAX) */
    public function store()
    {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $input = $this->getJsonInput();
        $items   = $input['items'] ?? [];
        $payment = (int)($input['payment'] ?? 0);

        if (empty($items)) {
            $this->jsonResponse(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        // Validate items and calculate total
        $total = 0;
        $validatedItems = [];

        foreach ($items as $item) {
            $product = $this->productModel->find($item['product_id'] ?? 0);
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => "Produk ID {$item['product_id']} tidak ditemukan"], 400);
            }

            $qty = (int)($item['quantity'] ?? 0);
            if ($qty <= 0) {
                $this->jsonResponse(['success' => false, 'message' => "Quantity tidak valid untuk {$product->name}"], 400);
            }
            if ($qty > $product->stock) {
                $this->jsonResponse(['success' => false, 'message' => "Stok {$product->name} tidak mencukupi (sisa: {$product->stock})"], 400);
            }

            $subtotal = (int)$product->price * $qty;
            $total += $subtotal;

            $validatedItems[] = [
                'product_id'   => (int)$product->id,
                'product_name' => $product->name,
                'barcode'      => $product->barcode,
                'quantity'     => $qty,
                'price'        => (int)$product->price,
                'subtotal'     => $subtotal
            ];
        }

        if ($payment < $total) {
            $this->jsonResponse(['success' => false, 'message' => 'Pembayaran kurang'], 400);
        }

        $change = $payment - $total;
        $code = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        $transactionId = $this->transactionModel->createWithItems([
            'transaction_code' => $code,
            'total_amount'     => $total,
            'payment_amount'   => $payment,
            'change_amount'    => $change,
            'user_id'          => $this->auth()['id'] ?? null,
        ], $validatedItems);

        if ($transactionId) {
            $this->jsonResponse([
                'success'          => true,
                'transaction_id'   => $transactionId,
                'transaction_code' => $code,
                'total'            => $total,
                'payment'          => $payment,
                'change'           => $change
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyimpan transaksi'], 500);
        }
    }

    public function detail($id = null)
    {
        if (!$id) $this->redirect('transactions');

        $transaction = $this->transactionModel->getWithItems($id);
        if (!$transaction) $this->redirect('transactions');

        $this->view('transactions/detail', [
            'title'       => 'Detail Transaksi',
            'transaction' => $transaction
        ]);
    }
}

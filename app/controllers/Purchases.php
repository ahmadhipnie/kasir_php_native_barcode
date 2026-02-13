<?php

class Purchases extends Controller
{
    private $purchaseModel;
    private $productModel;
    private $supplierModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->purchaseModel  = $this->model('Purchase');
        $this->productModel   = $this->model('Product');
        $this->supplierModel  = $this->model('Supplier');
    }

    public function index()
    {
        $this->view('purchases/index', [
            'title'     => 'Riwayat Pembelian',
            'purchases' => $this->purchaseModel->getRecent(50)
        ]);
    }

    public function create()
    {
        $this->view('purchases/create', [
            'title'     => 'Pembelian Baru',
            'suppliers' => $this->supplierModel->all('name ASC')
        ]);
    }

    /** API: Process purchase (called via AJAX) */
    public function store()
    {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $input      = $this->getJsonInput();
        $items      = $input['items'] ?? [];
        $supplierId = (int)($input['supplier_id'] ?? 0);
        $notes      = trim($input['notes'] ?? '');

        if (empty($items)) {
            $this->jsonResponse(['success' => false, 'message' => 'Daftar produk kosong'], 400);
        }

        $total = 0;
        $validatedItems = [];

        foreach ($items as $item) {
            $product = $this->productModel->find($item['product_id'] ?? 0);
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => "Produk tidak ditemukan"], 400);
            }

            $qty   = (int)($item['quantity'] ?? 0);
            $price = (int)($item['price'] ?? 0);

            if ($qty <= 0) {
                $this->jsonResponse(['success' => false, 'message' => "Quantity tidak valid untuk {$product->name}"], 400);
            }
            if ($price <= 0) {
                $this->jsonResponse(['success' => false, 'message' => "Harga beli tidak valid untuk {$product->name}"], 400);
            }

            $subtotal = $price * $qty;
            $total += $subtotal;

            $validatedItems[] = [
                'product_id'   => (int)$product->id,
                'product_name' => $product->name,
                'barcode'      => $product->barcode,
                'quantity'     => $qty,
                'price'        => $price,
                'subtotal'     => $subtotal
            ];
        }

        $code = 'PRC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        $purchaseId = $this->purchaseModel->createWithItems([
            'purchase_code' => $code,
            'supplier_id'   => $supplierId ?: null,
            'total_amount'  => $total,
            'notes'         => $notes,
            'user_id'       => $this->auth()['id'] ?? null
        ], $validatedItems);

        if ($purchaseId) {
            $this->jsonResponse([
                'success'       => true,
                'purchase_id'   => $purchaseId,
                'purchase_code' => $code,
                'total'         => $total
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyimpan pembelian'], 500);
        }
    }

    public function detail($id = null)
    {
        if (!$id) $this->redirect('purchases');

        $purchase = $this->purchaseModel->getWithItems($id);
        if (!$purchase) $this->redirect('purchases');

        $this->view('purchases/detail', [
            'title'    => 'Detail Pembelian',
            'purchase' => $purchase
        ]);
    }
}

<?php

class Products extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Produk',
            'products' => $this->productModel->all()
        ];

        $this->view('products/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'barcode' => $_POST['barcode'] ?? '',
                'name' => $_POST['name'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock' => $_POST['stock'] ?? 0,
                'category' => $_POST['category'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->productModel->create($data)) {
                $this->redirect('products');
            }
        }

        $this->view('products/add', ['title' => 'Tambah Produk']);
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'barcode' => $_POST['barcode'] ?? '',
                'name' => $_POST['name'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock' => $_POST['stock'] ?? 0,
                'category' => $_POST['category'] ?? '',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->productModel->update($id, $data)) {
                $this->redirect('products');
            }
        }

        $data = [
            'title' => 'Edit Produk',
            'product' => $this->productModel->find($id)
        ];

        $this->view('products/edit', $data);
    }

    public function delete($id)
    {
        if ($this->productModel->delete($id)) {
            $this->redirect('products');
        }
    }

    public function searchByBarcode()
    {
        $barcode = $_POST['barcode'] ?? '';
        $product = $this->productModel->findByBarcode($barcode);
        
        $this->jsonResponse($product ? $product : ['error' => 'Produk tidak ditemukan']);
    }
}

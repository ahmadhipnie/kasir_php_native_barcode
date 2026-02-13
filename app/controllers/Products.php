<?php

class Products extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->productModel  = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $products = $search
            ? $this->productModel->search($search)
            : $this->productModel->all('name ASC');

        $this->view('products/index', [
            'title'    => 'Daftar Produk',
            'products' => $products,
            'search'   => $search
        ]);
    }

    public function add()
    {
        if ($this->isPost()) {
            $barcode     = trim($_POST['barcode'] ?? '');
            $name        = trim($_POST['name'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $price       = (int)($_POST['price'] ?? 0);
            $stock       = (int)($_POST['stock'] ?? 0);

            // Resolve category name
            $categoryName = '';
            if ($category_id > 0) {
                $cat = $this->categoryModel->find($category_id);
                $categoryName = $cat ? $cat->name : '';
            }

            $errors = [];
            if (empty($barcode)) $errors[] = 'Barcode wajib diisi';
            if (empty($name))    $errors[] = 'Nama produk wajib diisi';
            if ($price <= 0)     $errors[] = 'Harga harus lebih dari 0';
            if ($stock < 0)      $errors[] = 'Stok tidak boleh negatif';
            if ($this->productModel->barcodeExists($barcode)) {
                $errors[] = 'Barcode sudah digunakan';
            }

            if (!empty($errors)) {
                $this->view('products/add', [
                    'title'      => 'Tambah Produk',
                    'errors'     => $errors,
                    'old'        => $_POST,
                    'categories' => $this->categoryModel->all('name ASC')
                ]);
                return;
            }

            $this->productModel->create([
                'barcode'     => $barcode,
                'name'        => $name,
                'category_id' => $category_id ?: null,
                'category'    => $categoryName,
                'price'       => $price,
                'stock'       => $stock,
            ]);

            $this->setFlash('success', 'Produk berhasil ditambahkan');
            $this->redirect('products');
        }

        $this->view('products/add', [
            'title'      => 'Tambah Produk',
            'categories' => $this->categoryModel->all('name ASC')
        ]);
    }

    public function edit($id = null)
    {
        if (!$id) $this->redirect('products');

        $product = $this->productModel->find($id);
        if (!$product) $this->redirect('products');

        if ($this->isPost()) {
            $barcode     = trim($_POST['barcode'] ?? '');
            $name        = trim($_POST['name'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $price       = (int)($_POST['price'] ?? 0);
            $stock       = (int)($_POST['stock'] ?? 0);

            $categoryName = '';
            if ($category_id > 0) {
                $cat = $this->categoryModel->find($category_id);
                $categoryName = $cat ? $cat->name : '';
            }

            $errors = [];
            if (empty($barcode)) $errors[] = 'Barcode wajib diisi';
            if (empty($name))    $errors[] = 'Nama produk wajib diisi';
            if ($price <= 0)     $errors[] = 'Harga harus lebih dari 0';
            if ($stock < 0)      $errors[] = 'Stok tidak boleh negatif';
            if ($this->productModel->barcodeExists($barcode, $id)) {
                $errors[] = 'Barcode sudah digunakan produk lain';
            }

            if (!empty($errors)) {
                $product->barcode     = $barcode;
                $product->name        = $name;
                $product->category_id = $category_id;
                $product->price       = $price;
                $product->stock       = $stock;

                $this->view('products/edit', [
                    'title'      => 'Edit Produk',
                    'product'    => $product,
                    'errors'     => $errors,
                    'categories' => $this->categoryModel->all('name ASC')
                ]);
                return;
            }

            $this->productModel->update($id, [
                'barcode'     => $barcode,
                'name'        => $name,
                'category_id' => $category_id ?: null,
                'category'    => $categoryName,
                'price'       => $price,
                'stock'       => $stock,
            ]);

            $this->setFlash('success', 'Produk berhasil diperbarui');
            $this->redirect('products');
        }

        $this->view('products/edit', [
            'title'      => 'Edit Produk',
            'product'    => $product,
            'categories' => $this->categoryModel->all('name ASC')
        ]);
    }

    public function delete($id = null)
    {
        if (!$id || !$this->isPost()) $this->redirect('products');

        try {
            if ($this->productModel->delete($id)) {
                $this->setFlash('success', 'Produk berhasil dihapus');
            } else {
                $this->setFlash('danger', 'Gagal menghapus produk');
            }
        } catch (Exception $e) {
            $this->setFlash('danger', 'Produk tidak dapat dihapus karena memiliki riwayat transaksi');
        }
        $this->redirect('products');
    }

    /** API: Search product by barcode (called from POS & Purchase) */
    public function searchByBarcode()
    {
        $barcode = trim($_POST['barcode'] ?? '');
        $context = trim($_POST['context'] ?? 'sale');
        $product = $this->productModel->findByBarcode($barcode);

        // For purchases, allow any product. For sales, require stock > 0
        if ($product && ($context === 'purchase' || $product->stock > 0)) {
            $this->jsonResponse([
                'success' => true,
                'product' => [
                    'id'       => (int)$product->id,
                    'barcode'  => $product->barcode,
                    'name'     => $product->name,
                    'price'    => (int)$product->price,
                    'stock'    => (int)$product->stock,
                    'category' => $product->category
                ]
            ]);
        } else {
            $msg = $product ? 'Stok produk habis' : 'Produk tidak ditemukan';
            $this->jsonResponse(['success' => false, 'message' => $msg], 404);
        }
    }

    /** API: Search products by keyword */
    public function search()
    {
        $keyword = trim($_GET['q'] ?? '');
        if (strlen($keyword) < 1) {
            $this->jsonResponse(['success' => true, 'products' => []]);
        }

        $products = $this->productModel->search($keyword);
        $result = [];
        foreach ($products as $p) {
            $result[] = [
                'id'       => (int)$p->id,
                'barcode'  => $p->barcode,
                'name'     => $p->name,
                'price'    => (int)$p->price,
                'stock'    => (int)$p->stock,
                'category' => $p->category
            ];
        }
        $this->jsonResponse(['success' => true, 'products' => $result]);
    }
}

<?php

class Suppliers extends Controller
{
    private $supplierModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->supplierModel = $this->model('Supplier');
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $suppliers = $search
            ? $this->supplierModel->search($search)
            : $this->supplierModel->getWithPurchaseCount();

        $this->view('suppliers/index', [
            'title'     => 'Daftar Supplier',
            'suppliers' => $suppliers,
            'search'    => $search
        ]);
    }

    public function add()
    {
        $this->requireAdmin();

        if ($this->isPost()) {
            $name    = trim($_POST['name'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $errors = [];
            if (empty($name)) $errors[] = 'Nama supplier wajib diisi';

            if (!empty($errors)) {
                $this->view('suppliers/add', [
                    'title' => 'Tambah Supplier',
                    'errors' => $errors,
                    'old' => $_POST
                ]);
                return;
            }

            $this->supplierModel->create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address
            ]);
            $this->setFlash('success', 'Supplier berhasil ditambahkan');
            $this->redirect('suppliers');
        }

        $this->view('suppliers/add', ['title' => 'Tambah Supplier']);
    }

    public function edit($id = null)
    {
        $this->requireAdmin();
        if (!$id) $this->redirect('suppliers');

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) $this->redirect('suppliers');

        if ($this->isPost()) {
            $name    = trim($_POST['name'] ?? '');
            $phone   = trim($_POST['phone'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $errors = [];
            if (empty($name)) $errors[] = 'Nama supplier wajib diisi';

            if (!empty($errors)) {
                $supplier->name = $name;
                $supplier->phone = $phone;
                $supplier->email = $email;
                $supplier->address = $address;
                $this->view('suppliers/edit', [
                    'title' => 'Edit Supplier',
                    'supplier' => $supplier,
                    'errors' => $errors
                ]);
                return;
            }

            $this->supplierModel->update($id, [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address
            ]);
            $this->setFlash('success', 'Supplier berhasil diperbarui');
            $this->redirect('suppliers');
        }

        $this->view('suppliers/edit', ['title' => 'Edit Supplier', 'supplier' => $supplier]);
    }

    public function delete($id = null)
    {
        $this->requireAdmin();
        if (!$id || !$this->isPost()) $this->redirect('suppliers');

        try {
            $this->supplierModel->delete($id);
            $this->setFlash('success', 'Supplier berhasil dihapus');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Supplier tidak dapat dihapus karena memiliki riwayat pembelian');
        }
        $this->redirect('suppliers');
    }
}

<?php

class Categories extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->categoryModel = $this->model('Category');
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $categories = $search
            ? $this->categoryModel->search($search)
            : $this->categoryModel->getWithProductCount();

        $this->view('categories/index', [
            'title'      => 'Daftar Kategori',
            'categories' => $categories,
            'search'     => $search
        ]);
    }

    public function add()
    {
        $this->requireAdmin();

        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            $errors = [];
            if (empty($name)) $errors[] = 'Nama kategori wajib diisi';
            if ($this->categoryModel->nameExists($name)) $errors[] = 'Nama kategori sudah digunakan';

            if (!empty($errors)) {
                $this->view('categories/add', [
                    'title' => 'Tambah Kategori',
                    'errors' => $errors,
                    'old' => $_POST
                ]);
                return;
            }

            $this->categoryModel->create(['name' => $name, 'description' => $desc]);
            $this->setFlash('success', 'Kategori berhasil ditambahkan');
            $this->redirect('categories');
        }

        $this->view('categories/add', ['title' => 'Tambah Kategori']);
    }

    public function edit($id = null)
    {
        $this->requireAdmin();
        if (!$id) $this->redirect('categories');

        $category = $this->categoryModel->find($id);
        if (!$category) $this->redirect('categories');

        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            $errors = [];
            if (empty($name)) $errors[] = 'Nama kategori wajib diisi';
            if ($this->categoryModel->nameExists($name, $id)) $errors[] = 'Nama kategori sudah digunakan';

            if (!empty($errors)) {
                $category->name = $name;
                $category->description = $desc;
                $this->view('categories/edit', [
                    'title' => 'Edit Kategori',
                    'category' => $category,
                    'errors' => $errors
                ]);
                return;
            }

            $this->categoryModel->update($id, ['name' => $name, 'description' => $desc]);
            $this->setFlash('success', 'Kategori berhasil diperbarui');
            $this->redirect('categories');
        }

        $this->view('categories/edit', ['title' => 'Edit Kategori', 'category' => $category]);
    }

    public function delete($id = null)
    {
        $this->requireAdmin();
        if (!$id || !$this->isPost()) $this->redirect('categories');

        try {
            $this->categoryModel->delete($id);
            $this->setFlash('success', 'Kategori berhasil dihapus');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Kategori tidak dapat dihapus karena masih digunakan produk');
        }
        $this->redirect('categories');
    }
}

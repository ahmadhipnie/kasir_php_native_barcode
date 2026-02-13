<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
</div>

<form method="POST" action="<?= BASE_URL ?>products/add">
    <div class="mb-3">
        <label for="barcode" class="form-label">Barcode</label>
        <input type="text" class="form-control" id="barcode" name="barcode" required>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Kategori</label>
        <input type="text" class="form-control" id="category" name="category" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" class="form-control" id="price" name="price" required>
    </div>
    <div class="mb-3">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" class="form-control" id="stock" name="stock" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= BASE_URL ?>products" class="btn btn-secondary">Batal</a>
</form>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

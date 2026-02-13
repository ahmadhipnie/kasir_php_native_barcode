<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= BASE_URL ?>products/add" class="btn btn-primary">Tambah Produk</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="productsTable">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product->barcode ?></td>
                        <td><?= $product->name ?></td>
                        <td><?= $product->category ?></td>
                        <td>Rp <?= number_format($product->price, 0, ',', '.') ?></td>
                        <td><?= $product->stock ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>products/edit/<?= $product->id ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= BASE_URL ?>products/delete/<?= $product->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada produk</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>

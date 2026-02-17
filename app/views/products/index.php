<?php ob_start(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><?= $title ?></h5>
        <div class="d-flex gap-2">
            <form class="d-flex" method="GET" action="<?= BASE_URL ?>products">
                <div class="input-group input-group-sm" style="width:250px">
                    <input type="text" class="form-control" name="q" placeholder="Cari produk..."
                        value="<?= htmlspecialchars($search ?? '') ?>" />
                    <button class="btn btn-outline-primary" type="submit"><i class="bx bx-search"></i></button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= BASE_URL ?>products" class="btn btn-outline-secondary"><i class="bx bx-x"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <a href="<?= BASE_URL ?>products/add" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> Tambah Produk
            </a>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Barcode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $i => $product): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($product->barcode) ?></code></td>
                            <td><strong><?= htmlspecialchars($product->name) ?></strong></td>
                            <td>
                                <?php if ($product->category): ?>
                                    <span class="badge bg-label-primary me-1"><?= htmlspecialchars($product->category) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?= number_format($product->price, 0, ',', '.') ?></td>
                            <td>
                                <?php if ($product->stock <= 0): ?>
                                    <span class="badge bg-label-danger">Habis</span>
                                <?php elseif ($product->stock <= 10): ?>
                                    <span class="badge bg-label-warning"><?= $product->stock ?></span>
                                <?php else: ?>
                                    <span class="badge bg-label-success"><?= $product->stock ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="<?= BASE_URL ?>products/edit/<?= $product->id ?>">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <form method="POST" action="<?= BASE_URL ?>products/delete/<?= $product->id ?>"
                                            onsubmit="return confirm('Yakin ingin menghapus produk ini?')" class="d-inline">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bx bx-package bx-lg"></i>
                            <p class="mt-2 mb-0">
                                <?= !empty($search) ? 'Tidak ditemukan produk dengan kata kunci tersebut' : 'Belum ada produk. <a href="' . BASE_URL . 'products/add">Tambah sekarang</a>' ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
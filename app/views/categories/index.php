<?php ob_start(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><?= $title ?></h5>
        <div class="d-flex gap-2">
            <form class="d-flex" method="GET" action="<?= BASE_URL ?>categories">
                <div class="input-group input-group-sm" style="width:240px">
                    <input type="text" class="form-control" name="q" placeholder="Cari kategori..."
                        value="<?= htmlspecialchars($search ?? '') ?>" />
                    <button class="btn btn-outline-primary" type="submit"><i class="bx bx-search"></i></button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= BASE_URL ?>categories" class="btn btn-outline-secondary"><i class="bx bx-x"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_URL ?>categories/add" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Produk</th>
                    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $i => $cat): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($cat->name) ?></strong></td>
                            <td><?= htmlspecialchars($cat->description ?? '-') ?></td>
                            <td><span class="badge bg-label-primary"><?= $cat->product_count ?? 0 ?></span></td>
                            <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="<?= BASE_URL ?>categories/edit/<?= $cat->id ?>">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="<?= BASE_URL ?>categories/delete/<?= $cat->id ?>"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bx bx-category bx-lg"></i>
                            <p class="mt-2 mb-0">
                                <?= !empty($search) ? 'Tidak ditemukan kategori dengan kata kunci tersebut' : 'Belum ada kategori' ?>
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
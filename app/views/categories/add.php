<?php ob_start(); ?>

<div class="col-xxl-8">
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><?= $title ?></h5>
            <a href="<?= BASE_URL ?>categories" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= $err ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>categories/add">
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="name">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            placeholder="Masukkan nama kategori" required autofocus />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="description">Deskripsi</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="description" name="description"
                            rows="3" placeholder="Deskripsi kategori (opsional)"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                        <a href="<?= BASE_URL ?>categories" class="btn btn-outline-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
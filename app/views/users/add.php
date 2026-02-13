<?php ob_start(); ?>

<div class="col-xxl-8">
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><?= $title ?></h5>
            <a href="<?= BASE_URL ?>users" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="<?= BASE_URL ?>users/add">
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="name">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            placeholder="Nama lengkap" required autofocus />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="username">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                            placeholder="Username untuk login" required />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="email">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            placeholder="Email (opsional)" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="password">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Minimal 6 karakter" minlength="6" required />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="role">Role</label>
                    <div class="col-sm-10">
                        <select class="form-select" id="role" name="role">
                            <option value="kasir" <?= ($old['role'] ?? '') === 'kasir' ? 'selected' : '' ?>>Kasir</option>
                            <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                        <a href="<?= BASE_URL ?>users" class="btn btn-outline-secondary ms-2">Batal</a>
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
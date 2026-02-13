<?php ob_start(); ?>

<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-user me-2"></i>Informasi Profil</h5>
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

                <form method="POST" action="<?= BASE_URL ?>profile/update">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($user->name) ?>" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user->username) ?>" disabled />
                        <small class="text-muted">Username tidak dapat diubah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($user->email ?? '') ?>" placeholder="Email" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="<?= htmlspecialchars($user->phone ?? '') ?>" placeholder="Nomor telepon" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">Alamat</label>
                        <textarea class="form-control" id="address" name="address"
                            rows="3" placeholder="Alamat"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?= ucfirst($user->role) ?>" disabled />
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-lock-alt me-2"></i>Ubah Password</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pwErrors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($pwErrors as $err): ?>
                                <li><?= $err ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>profile/password">
                    <div class="mb-3">
                        <label class="form-label" for="current_password">Password Lama</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="new_password">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            minlength="6" required />
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required />
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bx bx-lock me-1"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../app/views/layouts/header.php';
?>
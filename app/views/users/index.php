<?php ob_start(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><?= $title ?></h5>
        <div class="d-flex gap-2">
            <form class="d-flex" method="GET" action="<?= BASE_URL ?>users">
                <div class="input-group input-group-sm" style="width:240px">
                    <input type="text" class="form-control" name="q" placeholder="Cari user..."
                        value="<?= htmlspecialchars($search ?? '') ?>" />
                    <button class="btn btn-outline-primary" type="submit"><i class="bx bx-search"></i></button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= BASE_URL ?>users" class="btn btn-outline-secondary"><i class="bx bx-x"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <a href="<?= BASE_URL ?>users/add" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> Tambah User
            </a>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (!empty($users)): ?>
                    <?php $currentUserId = $_SESSION['user']['id'] ?? 0; ?>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($u->name) ?></strong></td>
                            <td><code><?= htmlspecialchars($u->username) ?></code></td>
                            <td><?= htmlspecialchars($u->email ?: '-') ?></td>
                            <td>
                                <?php if ($u->role === 'admin'): ?>
                                    <span class="badge bg-label-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-label-primary">Kasir</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="<?= BASE_URL ?>users/edit/<?= $u->id ?>">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <?php if ((int)$u->id !== $currentUserId): ?>
                                            <form method="POST" action="<?= BASE_URL ?>users/delete/<?= $u->id ?>"
                                                onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bx bx-group bx-lg"></i>
                            <p class="mt-2 mb-0">
                                <?= !empty($search) ? 'Tidak ditemukan user dengan kata kunci tersebut' : 'Belum ada user' ?>
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
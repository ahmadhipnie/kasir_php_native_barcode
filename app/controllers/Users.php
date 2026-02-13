<?php

class Users extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $users = $search
            ? $this->userModel->search($search)
            : $this->userModel->all('name ASC');

        $this->view('users/index', [
            'title'  => 'Manajemen User',
            'users'  => $users,
            'search' => $search
        ]);
    }

    public function add()
    {
        if ($this->isPost()) {
            $name     = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role     = $_POST['role'] ?? 'kasir';

            $errors = [];
            if (empty($name))     $errors[] = 'Nama wajib diisi';
            if (empty($username)) $errors[] = 'Username wajib diisi';
            if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
            if ($this->userModel->usernameExists($username)) $errors[] = 'Username sudah digunakan';

            if (!empty($errors)) {
                $this->view('users/add', [
                    'title' => 'Tambah User',
                    'errors' => $errors,
                    'old' => $_POST
                ]);
                return;
            }

            $this->userModel->create([
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => in_array($role, ['admin', 'kasir']) ? $role : 'kasir'
            ]);

            $this->setFlash('success', 'User berhasil ditambahkan');
            $this->redirect('users');
        }

        $this->view('users/add', ['title' => 'Tambah User']);
    }

    public function edit($id = null)
    {
        if (!$id) $this->redirect('users');

        $user = $this->userModel->find($id);
        if (!$user) $this->redirect('users');

        if ($this->isPost()) {
            $name     = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $role     = $_POST['role'] ?? 'kasir';
            $password = $_POST['password'] ?? '';

            $errors = [];
            if (empty($name))     $errors[] = 'Nama wajib diisi';
            if (empty($username)) $errors[] = 'Username wajib diisi';
            if ($this->userModel->usernameExists($username, $id)) $errors[] = 'Username sudah digunakan';

            if (!empty($errors)) {
                $user->name = $name;
                $user->username = $username;
                $user->email = $email;
                $user->role = $role;
                $this->view('users/edit', [
                    'title' => 'Edit User',
                    'user' => $user,
                    'errors' => $errors
                ]);
                return;
            }

            $data = [
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'role'     => in_array($role, ['admin', 'kasir']) ? $role : 'kasir'
            ];
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $user->name = $name;
                    $user->username = $username;
                    $user->email = $email;
                    $user->role = $role;
                    $this->view('users/edit', [
                        'title'  => 'Edit User',
                        'user'   => $user,
                        'errors' => ['Password minimal 6 karakter']
                    ]);
                    return;
                }
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $data);
            $this->setFlash('success', 'User berhasil diperbarui');
            $this->redirect('users');
        }

        $this->view('users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function delete($id = null)
    {
        if (!$id || !$this->isPost()) $this->redirect('users');
        if ((int)$id === $this->auth()['id']) {
            $this->setFlash('danger', 'Tidak dapat menghapus akun sendiri');
            $this->redirect('users');
        }

        $this->userModel->delete($id);
        $this->setFlash('success', 'User berhasil dihapus');
        $this->redirect('users');
    }
}

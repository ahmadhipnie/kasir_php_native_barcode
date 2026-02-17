<?php

class Profile extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $user = $this->userModel->find($this->auth()['id']);

        $this->view('profile/index', [
            'title' => 'Profil Saya',
            'user'  => $user
        ]);
    }

    public function update()
    {
        if (!$this->isPost()) $this->redirect('profile');

        $id      = $this->auth()['id'];
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $errors = [];
        if (empty($name)) $errors[] = 'Nama wajib diisi';

        if (!empty($errors)) {
            $user = $this->userModel->find($id);
            $user->name = $name;
            $user->email = $email;
            $user->phone = $phone;
            $user->address = $address;
            $this->view('profile/index', [
                'title' => 'Profil Saya',
                'user' => $user,
                'errors' => $errors
            ]);
            return;
        }

        $this->userModel->update($id, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ]);

        // Update session
        $_SESSION['user']['name']  = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;

        $this->setFlash('success', 'Profil berhasil diperbarui');
        $this->redirect('profile');
    }

    public function password()
    {
        if (!$this->isPost()) $this->redirect('profile');

        $id          = $this->auth()['id'];
        $current     = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->find($id);

        $errors = [];
        if (!password_verify($current, $user->password)) $errors[] = 'Password lama salah';
        if (strlen($newPass) < 6) $errors[] = 'Password baru minimal 6 karakter';
        if ($newPass !== $confirmPass) $errors[] = 'Konfirmasi password tidak cocok';

        if (!empty($errors)) {
            $this->view('profile/index', [
                'title' => 'Profil Saya',
                'user' => $user,
                'pwErrors' => $errors
            ]);
            return;
        }

        $this->userModel->update($id, ['password' => password_hash($newPass, PASSWORD_DEFAULT)]);
        $this->setFlash('success', 'Password berhasil diubah');
        $this->redirect('profile');
    }
}

<?php

class Auth extends Controller
{
    public function login()
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('dashboard');
        }

        if ($this->isPost()) {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->view('auth/login', [
                    'title' => 'Login',
                    'error' => 'Username dan password wajib diisi',
                    'old'   => ['username' => $username]
                ]);
                return;
            }

            $userModel = $this->model('User');
            $user = $userModel->authenticate($username, $password);

            if ($user) {
                $_SESSION['user'] = [
                    'id'       => (int)$user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'role'     => $user->role,
                    'phone'    => $user->phone ?? ''
                ];
                $this->redirect('dashboard');
            }

            $this->view('auth/login', [
                'title' => 'Login',
                'error' => 'Username atau password salah',
                'old'   => ['username' => $username]
            ]);
            return;
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function logout()
    {
        session_destroy();
        header("Location: " . BASE_URL . "auth/login");
        exit;
    }

    public function index()
    {
        $this->login();
    }
}

<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        $viewPath = '../app/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            http_response_code(404);
            die("View tidak ditemukan: $view");
        }
    }

    public function model($model)
    {
        $modelPath = '../app/models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        die("Model tidak ditemukan: $model");
    }

    public function redirect($url)
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    public function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function getJsonInput()
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /* ── Auth helpers ─────────────────────── */

    protected function requireLogin()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('auth/login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireLogin();
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->setFlash('danger', 'Akses ditolak');
            $this->redirect('dashboard');
        }
    }

    protected function auth()
    {
        return $_SESSION['user'] ?? null;
    }
}

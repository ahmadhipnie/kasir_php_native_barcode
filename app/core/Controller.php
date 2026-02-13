<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        
        $viewPath = '../app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View tidak ditemukan: $view");
        }
    }

    public function model($model)
    {
        $modelPath = '../app/models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die("Model tidak ditemukan: $model");
        }
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
}

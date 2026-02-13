<?php

class App
{
    protected $controller = 'Dashboard';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Resolve controller
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Resolve method
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        // Support both Apache mod_rewrite (?url=) and PHP built-in server
        $url = $_GET['url'] ?? trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (!empty($url)) {
            return explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
        }

        return [];
    }
}

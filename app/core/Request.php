<?php

namespace App\core;

class Request
{
    public function getRequestUrl(string $method = 'REQUEST_URI'): string
    {
        $url = $_SERVER[$method];
    
        if ($method === 'REQUEST_URI') {
            $pos = strpos($url, '?');
            return $pos ? substr($url, 0, $pos) : $url;
        }
        
        $url = str_contains($url, 'url=') ? '/' . substr($url, 4) : '/';
        $pos = strpos($url, '&');
        
        return $pos ? substr($url, 0, $pos) : $url;
    }
    
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): string
    {
        return $this->getMethod() === 'get';
    }

    public function isPost(): string
    {
        return $this->getMethod() === 'post';
    }

    public function getBody(): array
    {
        $data = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $data;
    }
}

<?php

namespace App\core;

class Request
{
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

    public function getRequestUrl(): string
    {
        $requrseUrl = $_SERVER['QUERY_STRING'];

        if (isset($requrseUrl)){
            $queryUrl = strpos($requrseUrl, '&');
            $requrseUrl = $queryUrl ? substr($requrseUrl, 0, $queryUrl) : $requrseUrl;
        }

        return '/' . $requrseUrl;
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

<?php

namespace App\core;

use App\core\Request;

class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function showJson(array $values): string
    {
        return json_encode($values, true);
    }

    public function readJson(): array
    {
        return json_decode(file_get_contents('php://input')) ?? [];
    }

    protected function redirectPage(string $url = ''): void
    {
        header("Location: {$_ENV['BASE_URL']}{$url}");
    }
}
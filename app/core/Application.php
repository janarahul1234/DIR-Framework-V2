<?php

namespace App\core;

use Dotenv\Dotenv;
use App\core\Route;
use App\core\Request;
use App\core\Response;
use App\core\Resolve;

class Application
{
    public static Application $app;

    private Request $request;
    private Response $response;
    public Route $route;
    public Resolve $resolve;

    public function __construct()
    {
        self::$app = $this;
        $this->loadDotEnv();

        $this->route = new Route();
        $this->request = new Request();
        $this->response = new Response();
        
        $this->resolve = new Resolve($this->route, $this->request, $this->response);
    }

    private function loadDotEnv(): void
    {
        try {
            $dotenv = Dotenv::createImmutable(ROOT_DIR);
            $dotenv->load();
        } catch (\Exception $e) {
            echo 'Please remove the env.example file!';
        }
    }

    public function run(): void
    {
        echo $this->resolve->response();
    }
}

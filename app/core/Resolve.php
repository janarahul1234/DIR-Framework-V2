<?php

namespace App\core;

class Resolve
{
    private string $layout;
    private Route $route;
    private Request $request;
    private Response $response;

    public function __construct(Route $route, Request $request, Response $response)
    {
        $this->route = $route;
        $this->request = $request;
        $this->response = $response;
    }

    public function response(): string
    {
        $method = $this->request->getMethod();
        $requestUrl = $this->request->getRequestUrl();
        $callback = $this->route->getCallback($method, $requestUrl);

        if ( ! $callback) {
            $this->response->setStatusCode(404);
            $view = $this->route->getfallback(404);

            return $view ? $this->render($view) : '404 | Not found';
        }

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        if (is_callable($callback[0])) {
            return call_user_func_array($callback[0], $callback[1]);
        }

        if (str_contains($callback[0][0], 'App')) {
            $callback[0][0] = new $callback[0][0]();
            $this->layout = $callback[0][0]->layout ?? '';

            return call_user_func_array([$callback[0][0], $callback[0][1] ?? 'index'], $callback[1]);
        }

        if (str_contains($callback[0], 'App')) {
            $callback[0] = new $callback[0]();
            $this->layout = $callback[0]->layout ?? '';
            
            return call_user_func([$callback[0], $callback[1] ?? 'index']);
        }

        return $this->render($callback[0], $callback[1]);
    }

    public function render(string $filename, array $values = []): string
    {
        $layoutContent = $this->loadLayout();
        $viewContent = $this->loadView($filename, $values);

        return $layoutContent ? str_replace('{{content}}', $viewContent, $layoutContent) : $viewContent;
    }

    public function loadView(string $filename, array $values = []): string
    {
        $filename = ROOT_DIR . "/app/views/{$filename}.php";

        if ( ! file_exists($filename)) {
            return "View file not found: {$filename}";
        }

        extract($values);
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    
    public function loadLayout(): string | bool
    {
        $filename = ROOT_DIR . "/app/views/layouts/{$this->layout}.php";

        if ( ! file_exists($filename)) {
            return false;
        }

        ob_start();
        include $filename;
        return ob_get_clean();
    }
}

<?php

namespace App\core\Razer;

class Razer extends Injector
{
    private string $view;
    private array $values;
    private string $layout;

    public function setView(string $view, array $values = []): void
    {
        $this->view = $view;
        $this->values = $values;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = "layouts/{$layout}";
    }

    public function bufferFile(string $fileName, array $variables = []): string
    {
        $fileName = ROOT_DIR . "/app/views/{$fileName}.php";

        if ( ! file_exists($fileName)) {
            return false;
        }

        extract($variables);
        ob_start();
        include $fileName;
        return ob_get_clean();
    }

    private function parseXLayout(string $bufferFile): bool | array
    {
        $layoutPattern = '/<x-layout>(.*?)<\/x-layout>/is';
        if ( ! preg_match($layoutPattern, $bufferFile, $matches)) { return false; }
    
        $layoutContent = $matches[1];
        $slotPattern = '/<x-slot\s+name="([^"]+)">(.*?)<\/x-slot>/is';
        preg_match_all($slotPattern, $layoutContent, $slotMatches, PREG_SET_ORDER);
    
        $variables = [];
        $remainingText = $layoutContent;
    
        foreach ($slotMatches as $match) {
            $variables[$match[1]] = $match[2];
            $remainingText = str_replace($match[0], '', $remainingText);
        }

        $variables['slot'] = trim($remainingText);
        return $variables;
    }

    protected function bind(): string
    {
        $view = $this->bufferFile($this->view);

        if ( ! $view) {
            return "View file not found: {$this->view}";
        }

        $view = $this->injectorAll($view, $this->values);
        $slots = $this->parseXLayout($view);

        if ( ! $slots) { 
            return $view; 
        }

        $layout = $this->bufferFile($this->layout);

        if ( ! $layout) {
            return "Layout file not found: {$this->layout}";
        }
        
        return $this->injectorAll($layout, $slots);
    }
}

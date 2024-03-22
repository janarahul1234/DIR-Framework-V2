<?php

namespace App\core\Razer;

class Injector
{
    public function injectorAll(string $bufferFile, array $variables): string
    {
        $bufferFile = $this->comment($bufferFile);
        $bufferFile = $this->variableInjector($bufferFile, $variables);
        $bufferFile = $this->functionInjector($bufferFile);

        return $bufferFile;
    }

    private function comment(string $bufferFile): string
    {
        $pattern = '/\{\{\-\-(.*?)\-\-\}\}/';
        preg_match_all($pattern, $bufferFile, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $bufferFile = str_replace($match[0], '', $bufferFile);
        }

        return $bufferFile;
    }

    private function variableInjector(string $bufferFile, array $variables): string
    {
        $pattern = '/\{\{\s*\$(.*?)\s*\}\}/';
        preg_match_all($pattern, $bufferFile, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $variable = trim($match[1]);
            $bufferFile = str_replace($match[0], $variables[$variable] ?? '', $bufferFile);
        }

        return $bufferFile;
    }

    private function functionInjector(string $bufferFile): string
    {
        $pattern = '/\{\{\s*@(.*?)\((.*?)\)\s*\}\}/';
        preg_match_all($pattern, $bufferFile, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $function = trim($match[1]);
            $args = trim($match[2]);
            $output = '';
            
            if (function_exists($function)) {
                $output = $this->functionExecutor($function, $args);
            }

            $bufferFile = str_replace($match[0], $output, $bufferFile);
        }

        return $bufferFile;
    }

    private function functionExecutor(callable $function, string $args = ''): string
    {
        $args = explode(',', $args);
        $variables = [];

        foreach ($args as $arg) {
            $variables[] = eval("return $arg;");
        }

        return call_user_func_array($function, $variables);
    }
}
<?php

use Ramsey\Uuid\Uuid;
use App\core\Application;

function view(string $name, array $values = []): string
{
    return Application::$app->resolve->render($name, $values);
}

function import(string $filename, array $values = []): string
{
    $filename = Application::$app->resolve->bufferFile($filename);
    return Application::$app->resolve->injectorAll($filename, $values);
}

function route(string $name): string
{
    return Application::$app->route->getRouteName($name);
}

function asset(string $filename): string
{
    return "{$_ENV['BASE_URL']}/{$filename}";
}

function generate_uuid(): string
{
    return Uuid::uuid4()->toString();
}

function allFieldsPresent(array $keys, array $values): bool
{
    foreach ($keys as $key) {
        if ( ! array_key_exists($key, $values) || empty($values[$key])) {
            return false;
        }
    }

    return true;
}

function isValidText(string $string): bool
{
  return ctype_alpha($string) && strlen($string) <= 255;
}

function validateEmail(string $string): bool
{
    return filter_var($string, FILTER_VALIDATE_EMAIL);
}

function validatePassword(string $string): bool
{
    $pattern = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&-+=()])(?=\\S+$).{8,}$/";
    return preg_match($pattern, $string);
}
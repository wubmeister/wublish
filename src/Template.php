<?php

namespace App;

class Template
{
    public static function render(string $template, array $params = [])
    {
        $file = dirname(__DIR__) . '/templates/' . $template . '.phtml';
        extract($params);
        ob_start();
        include $file;
        $content = ob_get_clean();

        return $content;
    }
}

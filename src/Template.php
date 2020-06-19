<?php

namespace App;

/**
 * Class to render templates
 */
class Template
{
    /**
     * Renders the template and returns the rendered content
     *
     * @param array $params Parameters which can be used in the template
     * @return string
     */
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

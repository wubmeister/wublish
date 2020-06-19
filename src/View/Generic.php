<?php

namespace App\View;

use App\Template;

class Generic implements ViewInterface
{
    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function getContent(array $params = [])
    {
        return Template::render($this->template, $params);
    }
}

<?php

namespace App\View;

use App\Template;

/**
 * Generic view
 *
 * @author Wubbo Bos <wubbo@wubbobos.nl>
 */
class Generic implements ViewInterface
{
    /** @var string $template The template name */
    protected $template;

    /**
     * Constructor
     *
     * @param string $template The template name, without extension
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Renders the content and returns it
     *
     * @param array $params Parameters which can be used in the template
     * @return string
     */
    public function getContent(array $params = [])
    {
        return Template::render($this->template, $params);
    }
}

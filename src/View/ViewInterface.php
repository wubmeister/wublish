<?php

namespace App\View;

/**
 * View Interface
 *
 * @author Wubbo Bos <wubbo@wubbobos.nl>
 */
interface ViewInterface
{
    /**
     * Renders the content and returns it
     *
     * @param array $params Parameters which can be used in the view
     * @return string
     */
    public function getContent(array $params = []);
}

<?php

namespace App\ViewController;

use App\View\Generic;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

class Login
{
    public function __invoke(ServerRequestInterface $request)
    {
        $view = new Generic("login");
        return new HtmlResponse($view->getContent());
    }
}

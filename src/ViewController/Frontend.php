<?php

namespace App\ViewController;

use App\Auth;
use App\View\Generic;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

class Frontend
{
    public function __invoke(ServerRequestInterface $request)
    {
        $user = Auth::authenticate();
        $view = new Generic("layout");
        return new HtmlResponse($view->getContent([ "user" => $user ]));
    }
}

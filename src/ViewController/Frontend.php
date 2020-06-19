<?php

namespace App\ViewController;

use App\Auth;
use App\View\Generic;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Frontend View Controller
 *
 * @author Wubbo Bos <wubbo@addnoise.nl>
 */
class Frontend
{
    /**
     * Shows the frontend
     *
     * @param ServerRequestInterface $request The request
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $user = Auth::authenticate();
        $view = new Generic("layout");
        return new HtmlResponse($view->getContent([ "user" => $user ]));
    }
}

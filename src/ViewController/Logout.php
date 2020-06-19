<?php

namespace App\ViewController;

use App\Auth;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

class Logout
{
    public function __invoke(ServerRequestInterface $request)
    {
        Auth::logout();
        return new RedirectResponse('/');
    }
}

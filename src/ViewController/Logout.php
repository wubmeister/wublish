<?php

namespace App\ViewController;

use App\Auth;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Logout View Controller
 *
 * @author Wubbo Bos <wubbo@addnoise.nl>
 */
class Logout
{
    /**
     * Clears the login information from the current session
     *
     * @param ServerRequestInterface $request The request
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request)
    {
        Auth::logout();
        return new RedirectResponse('/');
    }
}

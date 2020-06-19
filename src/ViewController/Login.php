<?php

namespace App\ViewController;

use App\Auth;
use App\View\Generic;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

class Login
{
    public function __invoke(ServerRequestInterface $request)
    {
        $values = [];
        $errors = [];

        if ($request->getMethod() == "POST") {
            $values = $request->getParsedBody();
            if (!isset($values['username']) || empty($values['username'])) {
                $errors['username'] = "Please enter your username";
            }
            if (!isset($values['password']) || empty($values['password'])) {
                $errors['password'] = "Please enter your password";
            }

            if (empty($errors)) {
                $user = Auth::login($values['username'], $values['password']);
                if (!$user) {
                    $errors['general'] = "Your username or password is incorrect";
                } else {
                    return new RedirectResponse('/');
                }
            }
        }

        $view = new Generic("login");
        return new HtmlResponse($view->getContent([ "values" => $values, "errors" => $errors ]));
    }
}

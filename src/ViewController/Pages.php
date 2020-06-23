<?php

namespace App\ViewController;

use App\Auth;
use App\Db;
use App\View\Generic;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Pages View Controller
 *
 * @author Wubbo Bos <wubbo@addnoise.nl>
 */
class Pages
{
    /**
     * Shows the login screen and in case of a POST request, tries to authenticate the user
     *
     * @param ServerRequestInterface $request The request
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $user = Auth::authenticate(true);

        $pages = Db::fetchAll("SELECT * FROM `page_menu` AS `m`
            LEFT JOIN `page` ON `page`.`id` = `m`.`page_id` ORDER BY `m`.`lft`");

        $view = new Generic("layout");
        return new HtmlResponse($view->getContent([
            'user' => $user,
            'pages' => $pages
        ]));
    }
}

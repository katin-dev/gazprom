<?php

namespace App\Action;

use Slim\Http\Request;
use Slim\Http\Response;

class IndexAction extends ActionAbstract
{
    public function __invoke(Request $request, Response $response)
    {
        return $this->view->render($response, 'index.twig', [
            'title' => 'Главная страница'
        ]);
    }
}
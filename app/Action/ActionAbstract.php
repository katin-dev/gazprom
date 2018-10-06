<?php

namespace App\Action;

use App\Lib\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class ActionAbstract
{
    /** @var \Slim\Views\Twig */
    protected $view;

    public function __construct(Container $container)
    {
        $this->view = $container->view;
    }

    abstract public function __invoke(Request $request, Response $response);
}
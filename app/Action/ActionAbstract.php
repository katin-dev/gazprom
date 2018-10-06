<?php

namespace App\Action;

use App\Lib\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class ActionAbstract
{
    /** @var \Slim\Views\Twig */
    protected $view;

    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->view = $container->view;
        $this->container = $container;
    }

    abstract public function __invoke(Request $request, Response $response);
}
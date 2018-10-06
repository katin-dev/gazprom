<?php

require __DIR__ . '/../vendor/autoload.php';

/** @var \Slim\App $app */
$app = require __DIR__ . '/../app/bootstrap.php';
$app->run();
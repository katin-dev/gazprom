#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = require 'bootstrap.php';

$consoleApp = new Application();

$consoleApp->add(new \App\Command\ImportCommand($app->getContainer()));

$consoleApp->run();
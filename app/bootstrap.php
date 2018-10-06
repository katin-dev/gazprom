<?php

use App\Action\IndexAction;
use App\Lib\Container;
use Slim\App;
use Slim\Views\Twig;

// Загружаем настройки приложения
$config = require __DIR__ . '/config.php';
if (file_exists(__DIR__ . '/config.loc.php')) {
    $config = array_merge($config, include __DIR__ . '/config.loc.php');
}

// Создаём контейнер для хранения сервисов
$container = new Container([
    'settings' => $config
]);

// База данных
$container['db'] = function (Container $c) {
    $config = parse_url($c->settings['db']);
    if (!$config) {
        throw new Exception('Invalid DB config URL');
    }

    return new PDO(
        'mysql:host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . ltrim($config['path'] , '/') . ';charset=utf8',
        $config['user'],
        $config['pass']
    );
};

// Шаблонизатор
$container['view'] = function (Container $c) {
    $view = new Twig($c->settings['template_path'], [
        'cache' => $c->settings['template_cache_path']
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $basePath));

    return $view;
};

$app = new App($container);

$app->get('/', IndexAction::class);

return $app;
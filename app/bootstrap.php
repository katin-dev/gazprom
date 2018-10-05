<?php

use App\Container;
use Slim\App;

// Загружаем настройки приложения
$config = require __DIR__ . '/config.php';

// Создаём контейнер для хранения сервисов
$container = new Container([
    'settings' => $config
]);

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

$app = new App($container);

$app->get('/', function () {
    $this->db;
});

return $app;
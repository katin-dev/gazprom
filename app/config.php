<?php

return [
    'db' => 'mysql://username:password@127.0.0.1:3306/db_name',
    'files' => [
        'visitors' => __DIR__ . '/../data/users.txt',
        'visits'   => __DIR__ . '/../data/request.txt',
    ],
    'template_path' => __DIR__ . '/templates',
    'template_cache_path' => __DIR__ . '/../var/cache',
];

<?php

return [
    'db' => 'mysql://username:password@127.0.0.1:3306/db_name',
    'files' => [
        'visitors' => __DIR__ . '/../data/visitors.txt',
        'visits'   => __DIR__ . '/../data/visits.txt',
    ],
    'template_path' => __DIR__ . '/templates',
    'template_cache_path' => false, // Кеш для простоты выключим
];

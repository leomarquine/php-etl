<?php

return [

    // If not provided, you can use the full path when working with files.
    'path' => env('ETL_PATH', base_path('etl')),

    // Currently supported databases: SQLite, MySQL, PostgreSQL
    'database' => [

        'default' => config('database.default'),

        'connections' => config('database.connections'),
    ],

];

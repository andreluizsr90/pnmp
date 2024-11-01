<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'sqlsrv',
            'host' => '192.168.1.4',
            'name' => 'pnmp',
            'user' => 'sa',
            'pass' => 'MyPass@word',
            'port' => '1433',
            'charset' => PDO::SQLSRV_ENCODING_UTF8,
            'dsn_options' => [
                "TrustServerCertificate" => true
            ]
        ],
    ],
    'version_order' => 'creation'
];

<?php declare(strict_types=1);

return [
    'db' => [
        'driver' => 'Pdo_Mysql', //Mysqli, Sqlsrv, Pdo_Sqlite, Pdo_Mysql, Pdo(= Other PDO Driver)
        'hostname' => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
    ],
];
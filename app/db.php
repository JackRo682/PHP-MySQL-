<?php
$config = require __DIR__ . '/config.php';

function get_pdo(): PDO
{
    static $pdo = null;
    global $config;
    if ($pdo === null) {
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);
    }
    return $pdo;
}

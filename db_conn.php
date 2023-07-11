<?php

$config_ini_path = getenv("CONFIG_INI_PATH") ?: "default.config.ini";

$config = parse_ini_file($config_ini_path, true);
$db_config = $config['db'];

$dsn = "mysql:host=" . $db_config['host']
    . ";port=" . $db_config['port']
    . ";dbname=" . $db_config['name']
    . ";charset=utf8mb4";
$options = [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pass'], $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

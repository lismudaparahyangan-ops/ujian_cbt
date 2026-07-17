<?php
$mysqlHost = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
$mysqlUser = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root';
$mysqlPass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';
$mysqlDb   = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'zyacbtpublic';
$mysqlPort = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: 3306;
$sqlFile   = __DIR__ . '/zyacbt-public-2024-05-05-dengan-database.sql';

if (!file_exists($sqlFile)) {
    die('SQL file not found: ' . $sqlFile . PHP_EOL);
}

$mysqli = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDb, (int) $mysqlPort);
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error . PHP_EOL);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    die('Unable to read SQL file.' . PHP_EOL);
}

if ($mysqli->multi_query($sql)) {
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
    echo "Database import completed successfully." . PHP_EOL;
} else {
    die('Import failed: ' . $mysqli->error . PHP_EOL);
}

$mysqli->close();

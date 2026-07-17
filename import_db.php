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

mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDb, (int) $mysqlPort);
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error . PHP_EOL);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    die('Unable to read SQL file.' . PHP_EOL);
}

$statements = preg_split('/;\s*(\r\n|\n|\r)/', $sql);
if ($statements === false) {
    die('Unable to split SQL file.' . PHP_EOL);
}

$executed = 0;
$skipped = 0;
$errors = [];
foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement === '' || strpos($statement, '--') === 0 || strpos($statement, '/*') === 0) {
        continue;
    }

    if (!$mysqli->query($statement)) {
        $errno = $mysqli->errno;
        $error = $mysqli->error;

        if (in_array($errno, [1050, 1060, 1062], true)) {
            $skipped++;
            continue;
        }

        $errors[] = "[$errno] $error";
        break;
    }

    $executed++;
}

$mysqli->close();

if (!empty($errors)) {
    echo "Import completed with errors:\n" . implode("\n", $errors) . PHP_EOL;
    exit(1);
}

echo "Database import completed successfully. Executed: $executed, skipped: $skipped." . PHP_EOL;

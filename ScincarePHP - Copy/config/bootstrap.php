<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

$config = require __DIR__ . '/config.php';
$pdo = Database::getConnection($config['db']);

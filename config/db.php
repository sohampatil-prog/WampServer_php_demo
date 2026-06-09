<?php
// This file lives in config/ — NOT directly in www/
// In real apps this would be outside the web root entirely

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // WAMP default
define('DB_PASS', '');           // WAMP default password is empty
define('DB_NAME', 'todo_app');

function getDB(): PDO {
    static $pdo = null;          // reuse the same connection in one request

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // throw on error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // rows as arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                   // real prepared statements
        ]);
    }

    return $pdo;
}
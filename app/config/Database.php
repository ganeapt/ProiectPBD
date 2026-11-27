<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    private const HOST = '127.0.0.1';
    private const PORT = '3307';
    private const DB_NAME = 'proiectpbd';
    private const USER = 'root';
    private const PASS = '';

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::HOST,
                self::PORT,
                self::DB_NAME
            );
            try {
                self::$instance = new PDO($dsn, self::USER, self::PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                die('Eroare critică: Nu s-a putut stabili conexiunea cu baza de date.');
            }
        }
        return self::$instance;
    }
}

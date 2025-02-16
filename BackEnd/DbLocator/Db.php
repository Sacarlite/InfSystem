<?php

class DB {
    private static $pdo = null;

    public static function connectFromJson($configFile) {
        if (!file_exists($configFile)) {
            http_response_code(500);
            echo json_encode(['error' => "Ошибка: Файл конфигурации '$configFile' не найден."], JSON_UNESCAPED_UNICODE);
        }

        $config = json_decode(file_get_contents($configFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(['error' => "Ошибка: Некорректный формат JSON в '$configFile'."], JSON_UNESCAPED_UNICODE);
        }

        $host = $config['host'];
        $port = $config['port'] ?? 3306; // Используем порт из конфигурации или 3306 по умолчанию
        $dbname = $config['dbname'];
        $username = $config['username'];
        $password = $config['password'];

        self::connect($host, $port, $dbname, $username, $password);
    }

    public static function connect($host, $port, $dbname, $username, $password) {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
                self::$pdo = new PDO($dsn, $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => "Ошибка подключения к базе данных: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
                exit();
            }
        }
    }

    public static function getConnection() {
        if (self::$pdo === null) {
            die("Ошибка: Подключение к базе данных не было инициализировано.");
        }
        return self::$pdo;
    }
}
?>

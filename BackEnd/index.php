<?php

// Разрешить все домены
header("Access-Control-Allow-Origin: *");

// Разрешить методы, которые может использовать клиент
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Разрешить заголовки, которые может отправлять клиент
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Обработка OPTIONS запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Если это запрос типа OPTIONS, просто завершаем его
    http_response_code(200);
    exit;
}
require_once 'DbLocator/Db.php';

// Подключаем маршруты
$routes = require 'routes.php';
DB::connectFromJson('Config\config.json');
// Получаем текущий URI и метод запроса
$uri = explode('?',$_SERVER['REQUEST_URI'])[0];
// Подключаем роутер и обрабатываем запрос
require_once 'router.php';
handleRequest($uri, $routes);
?>
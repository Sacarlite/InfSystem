<?php

// Функция для обработки маршрута
function matchRoute($uri, $routes) {
    
    foreach ($routes as $method => $routesForMethod) {
        // Если метод текущего запроса совпадает с маршрутом
        if ($_SERVER['REQUEST_METHOD'] === $method) {
            // Проходим по маршрутам для данного метода
            foreach ($routesForMethod as $route => $handler) {
                // Преобразуем маршрут с параметрами в регулярное выражение
                $pattern = str_replace('/', '\/', $route); // экранируем слэши

                // Проверяем, совпадает ли текущий URI с маршрутом
                if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
                    // Убираем первый элемент массива, который является полным совпадением
                    array_shift($matches);

                    // Возвращаем обработчик маршрута и параметры
                    return [$handler, $matches];
                }
            }
        }
    }
    return null;
}

// Функция для обработки маршрутов
function handleRequest($uri, $routes) {
    // Ищем соответствующий маршрут
    $routeInfo = matchRoute($uri, $routes);

    if ($routeInfo) {
        [$handler, $params] = $routeInfo;
        [$controllerName, $methodName] = $handler;

        // Подключаем соответствующий контроллер
        require_once "./Controllers/" . strtolower($controllerName) . ".php";
        $controller = new $controllerName();

        // Дополнительная обработка данных для некоторых методов
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Передаем массив ID в метод контроллера
            $Ids=file_get_contents('php://input');
            call_user_func([$controller, $methodName],  $Ids);
            exit;
        }
        
        // Если данных в теле запроса нет, то вызываем метод как обычно
        call_user_func_array([$controller, $methodName], $params);
        exit;
    }

    // Если маршрут не найден
    http_response_code(404);
    echo json_encode(['error' => 'Конечный путь не найден'], JSON_UNESCAPED_UNICODE);
}
?>

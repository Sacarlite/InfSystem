<?php

require_once './Models/Substance.php'; // Модель Substance
require_once './Validators/SubstanceValidator.php';
class SubstanceController {
    public function get(){
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if($id ==null){
            http_response_code(400);
        echo json_encode(['error' => 'Ошибка не задан обязательный параметр id'], JSON_UNESCAPED_UNICODE);
        return; // Завершаем выполнение метода 
        }
        if($id==='all'){
            $this->getAll();
        }
        else{
            $this->getById($id);
        }
    }
    /**
     * Получение всех веществ.
     * Отправляет данные в формате JSON.
     */
    public function getAll() {
        $substances = Substance::getAll();
        header('Content-Type: application/json');
        echo json_encode($substances, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Получение вещества по ID.
     * @param int $id ID вещества.
     */
    public function getById($id) {
        $substance = Substance::getById($id);
        header('Content-Type: application/json');

        if ($substance) {
            echo json_encode($substance, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(422);
            echo json_encode(['error' => "Ошибка вещество с данным идентификатором не найдено"], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Создание нового вещества.
     * Ожидает данные в формате JSON через метод POST.
     */
    public function create() {
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Проверка данных
            $validationResult = SubstanceValidator::validate($input);
             // Получаем все вещества из базы данных
            $substances = Substance::getAll();
            // Валидация: Проверяем, что каждый элемент массива — это положительное целое число
        foreach ($substances as $substance) {
            if ($substance['name']==$input['name']) {
               // Обработка исключений, связанных с добавлением вещества
            http_response_code(422 );
            echo json_encode(['error' => 'Вещество с таким названием уже существует'], JSON_UNESCAPED_UNICODE);
                return;
            }
        }
            if ($validationResult === true) {
                // Получаем IP-адрес клиента
                $ip_address = $_SERVER['REMOTE_ADDR'];
    
                // Добавляем новое вещество
                Substance::add(
                    $input['name'],
                    $input['density'],
                    $input['calorific_value'],
                    $input['min_concentration'],
                    $input['max_concentration'],
                    $input['substance_type_id'],
                    $ip_address
                );
    
                // Ответ клиенту
                echo json_encode(['message' => 'Вещество успешно добавлено'], JSON_UNESCAPED_UNICODE);
            } else {
                // Если валидация не прошла, возвращаем ошибку с описанием проблем
                http_response_code(422 );
                echo json_encode(['error' => 'Ошибка валидации исходных данных: ' . $validationResult], JSON_UNESCAPED_UNICODE);
            }
        } catch (Error $ex) {
            // Обработка исключений, связанных с добавлением вещества
            http_response_code(422 );
            echo json_encode(['error' => 'При добавлении вещества произошла ошибка'. $ex], JSON_UNESCAPED_UNICODE);
        }
    }
    

    // Метод для обновления вещества
    public function update() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
    
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Ошибка: отсутствует обязательный параметр id'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Проверка на успешную десериализацию
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Неверный формат идентификаторов'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Проверка данных
        $validationResult = SubstanceValidator::validate($input);
        
        if ($validationResult === true) {
            // Получаем IP-адрес клиента
            $ip_address = $_SERVER['REMOTE_ADDR'];
    
            // Обновляем вещество
            Substance::update(
                $id,
                $input['name'],
                $input['density'],
                $input['calorific_value'],
                $input['min_concentration'],
                $input['max_concentration'],
                $input['substance_type_id'],
                $ip_address
            );
    
            // Ответ клиенту
            echo json_encode(['message' => 'Данные вещества успешно обновлены'], JSON_UNESCAPED_UNICODE);
        } else {
            // Если валидация не прошла, возвращаем ошибку с описанием проблем
            http_response_code(422 );
            echo json_encode(['error' => 'Ошибка валидации исходных данных: ' . $validationResult], JSON_UNESCAPED_UNICODE);
        }
    }
    

    public function delete($args) {
        // Декодируем входной JSON в массив
        $ids = json_decode($args, true);
    
        // Валидация: Проверяем, что $ids — это массив
        if (empty($ids) || !is_array($ids)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ошибка массив идентификаторов пуст'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Валидация: Проверяем, что каждый элемент массива — это положительное целое число
        foreach ($ids as $id) {
            if (!is_int($id) || $id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Ошибка не верно задан идентификатор в массиве'], JSON_UNESCAPED_UNICODE);
                return;
            }
        }
    
        // Получаем все вещества из базы данных
        $substances = Substance::getAll();
    
        // Извлекаем ID существующих веществ
        $existingSubstances = array_column($substances, 'id_Substance');
    
        // Находим те ID, которые не существуют в базе
        $nonExistentIds = array_diff($ids, $existingSubstances);
    
        // Если есть несуществующие ID, отправляем их пользователю
        if (!empty($nonExistentIds)) {
            http_response_code(422);
            echo json_encode(['error' => 'Вещества с данными идентификаторами в базе не обнаружены ' . implode(', ', $nonExistentIds)], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        try {
            // Удаляем вещества по массиву ID
            Substance::deleteMultiple($ids);
            echo json_encode(['message' => 'Вещества с идентификаторами: ' . implode(', ', $ids) . ' успешно уддалены'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Ошибка при удалении веществ с идентификаторами " . implode(', ', $ids) . ": " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
    
    
}
?>
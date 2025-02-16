<?php

require_once './Models/Substance.php'; // Модель Substance
require_once './Validators/SubstanceValidator.php';
class SubstanceController {
    public function get(){
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if($id ==null){
            http_response_code(400); // Код ошибки 400 - Bad Request
        echo json_encode(['error' => 'ID parameter is required'], JSON_UNESCAPED_UNICODE);
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
            http_response_code(404);
            echo json_encode(['error' => "Substance with ID $id not found"], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Создание нового вещества.
     * Ожидает данные в формате JSON через метод POST.
     */
    public function create() {
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);

        // Проверка данных
        if (SubstanceValidator::validate($input)) {
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
            echo json_encode(['message' => 'Substance created successfully'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Invalid input data'], JSON_UNESCAPED_UNICODE);
        }
    }

    // Метод для обновления вещества
    public function update() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameter id'], JSON_UNESCAPED_UNICODE);
        return;
    }
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);

        // Проверка на успешную десериализацию
 if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON format for IDs'], JSON_UNESCAPED_UNICODE);
    return;
}

        // Проверка данных
        if (SubstanceValidator::validate($input)) {
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
            echo json_encode(['message' => 'Substance updated successfully'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Invalid input data'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function delete($args) {
        // Декодируем входной JSON в массив
        $ids = json_decode($args, true);
    
        // Валидация: Проверяем, что $ids — это массив
        if (empty($ids) || !is_array($ids)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or empty array of IDs'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Валидация: Проверяем, что каждый элемент массива — это положительное целое число
        foreach ($ids as $id) {
            if (!is_int($id) || $id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid ID in array. Each ID should be a positive integer'], JSON_UNESCAPED_UNICODE);
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
            http_response_code(404);
            echo json_encode(['error' => 'The following IDs do not exist: ' . implode(', ', $nonExistentIds)], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        try {
            // Удаляем вещества по массиву ID
            Substance::deleteMultiple($ids);
            echo json_encode(['message' => 'Substances with IDs ' . implode(', ', $ids) . ' deleted successfully'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Error deleting substances with IDs " . implode(', ', $ids) . ": " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
    
    
}
?>
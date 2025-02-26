<?php
require_once './Models/SubstanceType.php'; 
require_once './Validators/SubstanceTypeValidator.php';

class SubstanceTypeController {
    
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
     * Получение всех типов веществ.
     * Отправляет данные в формате JSON.
     */
    private function getAll() {
        $types = SubstanceType::getAll();
        header('Content-Type: application/json');
        echo json_encode($types, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Получение типа вещества по ID.
     * @param int $id ID типа вещества.
     */
    private function getById($id) {
        $type = SubstanceType::getById($id);
        header('Content-Type: application/json');

        if ($type) {
            echo json_encode($type, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(422);
            echo json_encode(['error' => "Substance type with ID $id not found"], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Создание нового типа вещества.
     * Ожидает данные в формате JSON через метод POST.
     */
    // Метод для создания типа вещества
    public function create() {
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Проверка на успешную десериализацию
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Неверный формат данных'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $substances = SubstanceType::getAll();
        // Валидация: Проверяем, что каждый элемент массива — это положительное целое число
    foreach ($substances as $substance) {
        if ($substance['s_t_name']==$input['name']) {
           // Обработка исключений, связанных с добавлением вещества
        http_response_code(422 );
        echo json_encode(['error' => 'Тип с таким названием уже существует'], JSON_UNESCAPED_UNICODE);
            return;
        }
    }
        // Проверка данных
        $validationResult = SubstanceTypeValidator::validate($input);
        if ($validationResult !== true) {
            http_response_code(422);
            echo json_encode(['error' => $validationResult], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Получаем IP-адрес клиента
        $ip_address = $_SERVER['REMOTE_ADDR'];
    
        try {
            // Добавляем новый тип вещества
            SubstanceType::add($input['name'], $ip_address);
    
            // Ответ клиенту
            echo json_encode(['message' => 'Тип вещества успешно добавлен'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка при добавлении типа вещества'. $e], JSON_UNESCAPED_UNICODE);
        }
    }
    
    // Метод для обновления типа вещества
    public function update() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        // Проверка на наличие обязательного параметра id
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Отсутствует обязательный параметр id'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Получаем данные из запроса
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Проверка на успешную десериализацию
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Неверный формат данных'], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Проверка данных
        $validationResult = SubstanceTypeValidator::validate($input);
        if ($validationResult !== true) {
            http_response_code(422);
            echo json_encode(['error' => $validationResult], JSON_UNESCAPED_UNICODE);
            return;
        }
    
        // Получаем IP-адрес клиента
        $ip_address = $_SERVER['REMOTE_ADDR'];
    
        try {
            // Обновляем тип вещества
            SubstanceType::update($id, $input['name'], $ip_address);
    
            // Ответ клиенту
            echo json_encode(['message' => 'Тип вещества успешно обновлён'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка при обновлении типа вещества'], JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function delete($ids) {
           // Декодируем входной JSON в массив
    $ids = json_decode($ids, true);

 // Проверка на успешную десериализацию
 if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Ошибка десериализации вводных данных массива идентификаторов'], JSON_UNESCAPED_UNICODE);
    return;
}

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
            echo json_encode(['error' => 'Ошибка валидации идентификаторов'], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // Получаем все типы веществ из базы данных
    $substanceTypes = SubstanceType::getAll();

    // Извлекаем ID существующих типов веществ
    $existingSubstanceTypes = array_column($substanceTypes, 'id_Substance_type');

    // Находим те ID, которые не существуют в базе
    $nonExistentIds = array_diff($ids, $existingSubstanceTypes);

    // Если есть несуществующие ID, отправляем их пользователю
    if (!empty($nonExistentIds)) {
        http_response_code(422);
        echo json_encode(['error' => 'Ошибка удаления типы с данными идентификаторами не существуют: ' . implode(', ', $nonExistentIds)], JSON_UNESCAPED_UNICODE);
        return;
    }
        try {
            // Удаляем вещества по массиву ID
            SubstanceType::deleteMultiple($ids);
            echo json_encode(['message' => 'Типы с идентификаторами ' . implode(', ', $ids) . ' Успешно удалены'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => "Ошибка при удалении типов веществ " . implode(', ', $ids) . ": " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
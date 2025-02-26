<?php

require_once './DbLocator/Db.php'; // Класс для подключения к базе данных

class Substance {
    // Получение всех веществ с информацией о типе вещества
    public static function getAll() {
        $pdo = DB::getConnection(); // Получаем подключение через класс DB
        $stmt = $pdo->query("SELECT s.*, st.s_t_name AS substance_type_name
                             FROM `Substance` s
                             LEFT JOIN `Substance_type` st ON s.Substance_type_id_Substance_type = st.id_Substance_type");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получение вещества по ID с информацией о типе вещества
    public static function getById($id) {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare("SELECT s.*, st.s_t_name AS substance_type_name
                               FROM `Substance` s
                               LEFT JOIN `Substance_type` st ON s.Substance_type_id_Substance_type = st.id_Substance_type
                               WHERE s.id_Substance = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Метод для добавления нового вещества
    public static function add($name, $density, $calorific_value, $min_concentration, $max_concentration, $substance_type_id, $ip_address) {
        $pdo = Db::getConnection();
        
        // Подготовленный запрос с новыми полями
        $stmt = $pdo->prepare("INSERT INTO `Substance` 
                                (name, density, calorific_value, min_concentration, max_concentration, Substance_type_id_Substance_type, ip_address, redact_time) 
                               VALUES 
                                (:name, :density, :calorific_value, :min_concentration, :max_concentration, :substance_type_id, :ip_address, NOW())");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':density', $density);
        $stmt->bindParam(':calorific_value', $calorific_value);
        $stmt->bindParam(':min_concentration', $min_concentration);
        $stmt->bindParam(':max_concentration', $max_concentration);
        $stmt->bindParam(':substance_type_id', $substance_type_id);
        if ($ip_address === '::1') {
            $ip_address = '127.0.0.1';
        }
        $stmt->bindParam(':ip_address', $ip_address);

        $stmt->execute();
    }

    // Метод для обновления вещества
    public static function update($id, $name, $density, $calorific_value, $min_concentration, $max_concentration, $substance_type_id, $ip_address) {
        $pdo = Db::getConnection();

        // Подготовленный запрос для обновления вещества
        $stmt = $pdo->prepare("UPDATE `Substance` 
                               SET `name` = :name, 
                                   `density` = :density, 
                                   `calorific_value` = :calorific_value,
                                   `min_concentration` = :min_concentration,
                                   `max_concentration` = :max_concentration,
                                   `Substance_type_id_Substance_type` = :substance_type_id,
                                   `ip_address` = :ip_address,
                                   `redact_time` = NOW() 
                               WHERE `id_Substance` = :id");

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':density', $density);
        $stmt->bindParam(':calorific_value', $calorific_value);
        $stmt->bindParam(':min_concentration', $min_concentration);
        $stmt->bindParam(':max_concentration', $max_concentration);
        $stmt->bindParam(':substance_type_id', $substance_type_id);
        if ($ip_address === '::1') {
            $ip_address = '127.0.0.1';
        }
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function deleteMultiple($ids) {
        $pdo = DB::getConnection();
        try {
           // Соединяем ID в строку для удобства использования в SQL-запросе
    $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("DELETE FROM `Substance` WHERE `id_Substance` IN ($idPlaceholders)");

    // Привязываем ID в запрос
    foreach ($ids as $index => $id) {
        $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
    }

    $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception('Ошибка при удалении вещества: ' . $e->getMessage());
        }
    }
}
?>

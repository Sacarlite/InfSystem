<?php

require_once './DbLocator/Db.php'; // Класс для подключения к базе данных

class SubstanceType {
    /**
     * Получение всех типов веществ.
     *
     * @return array Возвращает массив всех записей из таблицы `Substance_type`.
     */
    public static function getAll() {
        $pdo = DB::getConnection();
        $stmt = $pdo->query("SELECT * FROM `Substance_type`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получение типа вещества по ID.
     *
     * @param int $id ID типа вещества.
     * @return array|null Возвращает массив данных типа вещества или null, если запись не найдена.
     */
    public static function getById($id) {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM `Substance_type` WHERE `id_Substance_type` = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Добавление нового типа вещества.
     *
     * @param string $name Название типа вещества.
     * @return void
     */
    public static function add($name, $ip_address) {
        $pdo = Db::getConnection();
    
        // Подготовленный запрос с новыми полями
        $stmt = $pdo->prepare("INSERT INTO `Substance_type` 
                                (s_t_name, ip_address, redact_time) 
                                VALUES 
                                (:name, :ip_address, NOW())");
    
        $stmt->bindParam(':name', $name);
        if ($ip_address === '::1') {
            $ip_address = '127.0.0.1';
        }
        $stmt->bindParam(':ip_address', $ip_address);
    
        // Выполняем запрос
        $stmt->execute();
    }

    /**
     * Обновление существующего типа вещества.
     *
     * @param int $id ID типа вещества.
     * @param string $name Новое название типа вещества.
     * @return void
     */
    public static function update($id, $name, $ip_address) {
        $pdo = Db::getConnection();
    
        // Подготовленный запрос для обновления типа вещества
        $stmt = $pdo->prepare("UPDATE `Substance_type` 
                               SET `s_t_name` = :name, 
                                   `ip_address` = :ip_address,
                                   `redact_time` = NOW() 
                               WHERE `id_Substance_type` = :id");
    
        $stmt->bindParam(':name', $name);
        if ($ip_address === '::1') {
            $ip_address = '127.0.0.1';
        }
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        // Выполняем запрос
        $stmt->execute();
    }

    public static function deleteMultiple($ids) {
        $pdo = DB::getConnection();
        try {
            // Формируем запрос для удаления нескольких записей
            $inQuery = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM `Substance_type` WHERE `id_Substance_type` IN ($inQuery)");
            $stmt->execute($ids);
        } catch (PDOException $e) {
            throw new Exception('Ошибка при удалении типов вещества: ' . $e->getMessage());
        }
    }
}
?>

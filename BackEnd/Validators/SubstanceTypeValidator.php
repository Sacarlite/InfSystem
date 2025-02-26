<?php

class SubstanceTypeValidator {
    /**
     * Валидация данных для сущности `SubstanceType`.
     *
     * @param array $data Данные для проверки.
     * @return bool true, если данные корректны; иначе false.
     */
    public static function validate($data) {
        $errors = [];
       // Проверка на наличие обязательных данных
    if (!isset($data['name']) || !is_string($data['name'])) {
        $errors[] = 'Название вещества должно быть строкой';
    }
    // Если есть ошибки, возвращаем их в виде строки, иначе true
    if (count($errors) > 0) {
        return implode(", ", $errors);
    }

    return true;
    }
}
?>

<?php

class SubstanceTypeValidator {
    /**
     * Валидация данных для сущности `SubstanceType`.
     *
     * @param array $data Данные для проверки.
     * @return bool true, если данные корректны; иначе false.
     */
    public static function validate($data) {
        return isset($data['name']) && is_string($data['name']);
    }
}
?>

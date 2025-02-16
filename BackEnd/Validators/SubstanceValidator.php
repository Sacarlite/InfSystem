<?php

class SubstanceValidator {
    /**
     * Валидация данных для сущности `Substance`.
     *
     * @param array $data Данные для проверки.
     * @return bool true, если данные корректны; иначе false.
     */
    public static function validate($data) {
        return isset($data['name'], $data['density'], $data['calorific_value'], $data['min_concentration'], $data['max_concentration'], $data['substance_type_id']) &&
            is_string($data['name']) &&
            is_numeric($data['density']) &&
            is_numeric($data['calorific_value']) &&
            is_numeric($data['min_concentration']) &&
            is_numeric($data['max_concentration']) &&
            is_numeric($data['substance_type_id'])&&
            $data['density']>0 &&
            $data['calorific_value']>0&&
            $data['min_concentration']>0&&
            $data['max_concentration']>0&&
            $data['max_concentration']>=$data['min_concentration'];
    }
}
?>

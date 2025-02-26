<?php

class SubstanceValidator {
    /**
     * Валидация данных для сущности `Substance`.
     *
     * @param array $data Данные для проверки.
     * @return bool true, если данные корректны; иначе false.
     */
    public static function validate($data) { 
        $errors = [];
    
        // Установим максимальные и минимальные значения для полей
        $maxDensity = 1000; // Пример максимального значения для плотности
        $maxCalorificValue = 10000; // Пример максимального значения для теплоты сгорания
        $maxConcentration = 100; // Пример максимального значения для концентрации
    
        // Проверка на существование обязательных полей
        if (!isset($data['name'], $data['density'], $data['calorific_value'], $data['min_concentration'], $data['max_concentration'], $data['substance_type_id'])) {
            $errors[] = "Все поля должны быть заполнены.";
        }
    
        // Проверка типа для 'name'
        if (!is_string($data['name'])) {
            $errors[] = "Поле 'name' должно быть строкой.";
        }
    
        // Проверка для 'density'
        if (!is_numeric($data['density']) || $data['density'] <= 0) {
            $errors[] = "Поле плотности должно быть положительным числом.";
        } elseif ($data['density'] >= $maxDensity) {
            $errors[] = "Значение плотности превышает максимальное значение ($maxDensity).";
        }
    
        // Проверка для 'calorific_value'
        if (!is_numeric($data['calorific_value']) || $data['calorific_value'] < 0) {
            $errors[] = "Поле теплоты сгорания должно быть положительным числом.";
        } elseif ($data['calorific_value'] >= $maxCalorificValue) {
            $errors[] = "Значение теплоты сгорания превышает максимальное значение ($maxCalorificValue).";
        }
    
        // Проверка для 'min_concentration'
        if (!is_numeric($data['min_concentration']) || $data['min_concentration'] < 0) {
            $errors[] = "Поле минимальной концентрации должно быть положительным числом.";
        } elseif ($data['min_concentration'] > $maxConcentration) {
            $errors[] = "Значение минимальной концентрации превышает максимальное значение ($maxConcentration).";
        }
    
        // Проверка для 'max_concentration'
        if (!is_numeric($data['max_concentration']) || $data['max_concentration'] <= 0) {
            $errors[] = "Поле максимальной концентрации должно быть положительным числом.";
        } elseif ($data['max_concentration'] > $maxConcentration) {
            $errors[] = "Значение максимальной концентрации превышает максимальное значение ($maxConcentration).";
        }
    
        // Проверка на соответствие min_concentration и max_concentration
        if ($data['max_concentration'] < $data['min_concentration']) {
            $errors[] = "Поле максимальной концентрации  не может быть меньше минимальной концентрации.";
        }
    
        // Проверка для 'substance_type_id'
        if (!is_numeric($data['substance_type_id'])) {
            $errors[] = "Поле 'substance_type_id' должно быть числом.";
        }
    
        // Если есть ошибки, возвращаем их в виде строки, иначе true
        if (count($errors) > 0) {
            return implode(", ", $errors);
        }
    
        return true;
    }
    
}
?>

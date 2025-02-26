// src/components/EditSubstanceModal.js
import React, { useState, useEffect } from 'react';
import { Modal, Button, Form } from 'react-bootstrap';
import ApiService from '../services/ApiService'; // Подключаем ApiService
import './css/edit.css'; // Подключаем стили

const EditSubstanceModal = ({ show, handleClose, substanceToEdit }) => {
  const [name, setName] = useState('');
  const [density, setDensity] = useState('');
  const [calorificValue, setCalorificValue] = useState('');
  const [minConcentration, setMinConcentration] = useState('');
  const [maxConcentration, setMaxConcentration] = useState('');
  const [substanceType, setSubstanceType] = useState('');  // Сохраняем ID типа вещества
  const [substanceTypes, setSubstanceTypes] = useState([]);
  const [newSubstanceType, setNewSubstanceType] = useState(''); // Для нового типа вещества
  const [errors, setErrors] = useState({}); // Хранение ошибок
  const fetchSubstanceTypes = async () => {
    try {
      const typesData = await ApiService.getSubstanceTypes();
      setSubstanceTypes(typesData);
    } catch (error) {
     alert(error);
    }
  };
  const fetchAndAddSubstanceTypes = async (newType) => {
    try {
      // Получаем обновленный список типов веществ
    const typesData = await ApiService.getSubstanceTypes();
    setSubstanceTypes(typesData);

    // Выбираем последний добавленный тип (после обновления списка)
    const newType = typesData[typesData.length - 1];
    setSubstanceType(newType.id_Substance_type);  // Выбираем новый тип вещества
    } catch (error) {
     alert(error);
    }
  };
  useEffect(() => {
   

    fetchSubstanceTypes();

    // Если редактируем вещество, заполняем форму данными
    if (substanceToEdit) {
      setName(substanceToEdit.name);
      setDensity(substanceToEdit.density);
      setCalorificValue(substanceToEdit.calorific_value);
      setMinConcentration(substanceToEdit.min_concentration);
      setMaxConcentration(substanceToEdit.max_concentration);
      setSubstanceType(substanceToEdit.Substance_type_id_Substance_type);  // Сохраняем ID типа вещества
    } else {
      // Если добавляем новое вещество, очищаем форму
      setName('');
      setDensity('');
      setCalorificValue('');
      setMinConcentration('');
      setMaxConcentration('');
      setSubstanceType('');
    }
  }, [substanceToEdit]);

  const validateForm = () => {
    const errors = {};

    if (!name) errors.name = "Название вещества обязательно";
    if (!density || isNaN(density) || density <= 0) errors.density = "Введите корректную плотность";
    if (!calorificValue || isNaN(calorificValue) || calorificValue <= 0) errors.calorificValue = "Введите корректную теплоту сгорания";
    if (!minConcentration || isNaN(minConcentration) || minConcentration < 0 || minConcentration > 100) errors.minConcentration = "Концентрация должна быть в диапазоне от 0 до 100";
    if (!maxConcentration || isNaN(maxConcentration) || maxConcentration < 0 || maxConcentration > 100) errors.maxConcentration = "Концентрация должна быть в диапазоне от 0 до 100";
    if (!substanceType) errors.substanceType = "Выберите тип вещества";

    setErrors(errors);

    return Object.keys(errors).length === 0; // Если ошибок нет, возвращаем true
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validateForm()) {
      alert("Пожалуйста, исправьте ошибки в форме.");
      return; // Останавливаем выполнение, если есть ошибки
    }

    const substanceData = {
      name,
      density: parseFloat(density),
      calorific_value: parseFloat(calorificValue),
      min_concentration: parseFloat(minConcentration),
      max_concentration: parseFloat(maxConcentration),
      substance_type_id: substanceType,  // Передаем только ID типа вещества
    };

    try {
      if (substanceToEdit) {
        // Редактирование
        await ApiService.updateSubstance(substanceToEdit.id_Substance, substanceData);
        alert("Данные вещества успешно отредактированы")
      } else {
        // Добавление
        await ApiService.addSubstance(substanceData);
        alert("Вещество успешно добавлено")
      }
      handleClose(); // Закрыть модальное окно после успешного добавления/редактирования
    } catch (error) {
      alert(error);
    }
  };
  const handleAddNewSubstanceType = () => {
    const newType = prompt("Введите новый тип вещества:");
    if (newType) {
      // Создаем новый тип вещества и обновляем список типов
      ApiService.addSubstanceType({ name: newType })
        .then(() => {
          alert('Тип вещества успешно добавлен');
          fetchAndAddSubstanceTypes(newType);
             })
        .catch((error) => {
          alert(error);
        });
      
       
    }
  };
  return (
    <Modal show={true} onHide={handleClose}>
      <Modal.Header>
        <Modal.Title>{substanceToEdit ? 'Редактировать вещество' : 'Добавить вещество'}</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="formName">
            <Form.Label>Название</Form.Label>
            <Form.Control
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="Введите название вещества"
              isInvalid={!!errors.name}
              required
            />
            <Form.Control.Feedback type="invalid">
              {errors.name}
            </Form.Control.Feedback>
          </Form.Group>
          <Form.Group controlId="formDensity">
            <Form.Label>Плотность (кг/м³)</Form.Label>
            <Form.Control
              type="number"
              step="0.01"
              value={density}
              onChange={(e) => setDensity(e.target.value)}
              placeholder="Введите плотность"
              isInvalid={!!errors.density}
              required
            />
            <Form.Control.Feedback type="invalid">
              {errors.density}
            </Form.Control.Feedback>
          </Form.Group>
          <Form.Group controlId="formCalorificValue">
            <Form.Label>Теплота сгорания (Дж)</Form.Label>
            <Form.Control
              type="number"
              step="0.01"
              value={calorificValue}
              onChange={(e) => setCalorificValue(e.target.value)}
              placeholder="Введите теплоту сгорания"
              isInvalid={!!errors.calorificValue}
              required
            />
            <Form.Control.Feedback type="invalid">
              {errors.calorificValue}
            </Form.Control.Feedback>
          </Form.Group>
          <Form.Group controlId="formMinConcentration">
            <Form.Label>Минимальная концентрация (%)</Form.Label>
            <Form.Control
              type="number"
              value={minConcentration}
              onChange={(e) => setMinConcentration(e.target.value)}
              placeholder="Введите минимальную концентрацию"
              isInvalid={!!errors.minConcentration}
              required
            />
            <Form.Control.Feedback type="invalid">
              {errors.minConcentration}
            </Form.Control.Feedback>
          </Form.Group>

          <Form.Group controlId="formMaxConcentration">
            <Form.Label>Максимальная концентрация (%)</Form.Label>
            <Form.Control
              type="number"
              value={maxConcentration}
              onChange={(e) => setMaxConcentration(e.target.value)}
              placeholder="Введите максимальную концентрацию"
              isInvalid={!!errors.maxConcentration}
              required
            />
            <Form.Control.Feedback type="invalid">
              {errors.maxConcentration}
            </Form.Control.Feedback>
          </Form.Group>

          <Form.Group controlId="formSubstanceType" className="form-group-substance-type">
  <Form.Label>Тип вещества</Form.Label>
  <Form.Control
    as="select"
    value={substanceType}
    onChange={(e) => setSubstanceType(e.target.value)}
    isInvalid={!!errors.substanceType}
    required
  >
    <option value="">Выберите тип вещества</option>
    {substanceTypes.map((type) => (
      <option key={type.id_Substance_type} value={type.id_Substance_type}>
        {type.s_t_name}
      </option>
    ))}
  </Form.Control>
  <Button className="add-new-type-btn" onClick={handleAddNewSubstanceType}>
    <i className="fas fa-plus"></i> Добавить новый тип
  </Button>
  <Form.Control.Feedback type="invalid">
    {errors.substanceType}
  </Form.Control.Feedback>
</Form.Group>

         
        </Form>
      </Modal.Body>
      <Modal.Footer>
      <Button variant="primary" type="submit"  onClick={handleSubmit}>
            {substanceToEdit ? 'Сохранить изменения' : 'Добавить'}
          </Button>
        <Button variant="secondary" onClick={handleClose}>
          Закрыть
        </Button>
      </Modal.Footer>
    </Modal>
  );
};

export default EditSubstanceModal;

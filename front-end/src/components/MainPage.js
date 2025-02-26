import React, { useState, useEffect } from 'react';
import { Button } from 'react-bootstrap';
import { Table, Form } from 'react-bootstrap';
import ApiService from '../services/ApiService'; // Подключаем ApiService
import './css/table.css'; // Подключаем стили
import EditSubstanceModal from './EditModal';  // Импортируем компонент модального окна


const SubstancesTable = () => {
  const [substances, setSubstances] = useState([]);
  const [filteredSubstances, setFilteredSubstances] = useState([]);
  const [selectedRows, setSelectedRows] = useState(new Set());
  const [currentPage, setCurrentPage] = useState(1);
  const [recordsPerPage, setRecordsPerPage] = useState(10);
  const [searchQuery, setSearchQuery] = useState(''); // Поиск по названию
  const [minDensity, setMinDensity] = useState(''); // Минимальная плотность
  const [maxDensity, setMaxDensity] = useState(''); // Максимальная плотность
  const [minCalorificValue, setMinCalorificValue] = useState(''); // Минимальная теплота сгорания
  const [maxCalorificValue, setMaxCalorificValue] = useState(''); // Максимальная теплота сгорания
  const [filterEnabled, setFilterEnabled] = useState(false); // Состояние для чекбокса плотности
  const [calorificFilterEnabled, setCalorificFilterEnabled] = useState(false); // Состояние для чекбокса теплоты сгорания
  const [substanceTypes, setSubstanceTypes] = useState([]); // Состояние для типов веществ
  const [selectedType, setSelectedType] = useState(''); // Состояние для выбранного типа вещества
  const [typeFilterEnabled, setTypeFilterEnabled] = useState(false); // Состояние для чекбокса типа вещества
  const [errorMessage, setErrorMessage] = useState(''); // Для ошибок
  const [sortConfig, setSortConfig] = useState({ key: null, direction: 'asc' }); // Добавляем конфиг для сортировки
  const [showEditModal, setShowAddModal] = useState(false); // Состояние для отображения модального окна
  const [isFormVisible, setIsFormVisible] = useState(false); // Состояние для переключения между таблицей и формой
  const [substanceToEdit, setSubstanceToEdit] = useState(null); // Состояние для данных вещества, которое редактируем
  const fetchSubstancesAndTypes = async () => {
    try {
      // Получаем все вещества
      const substancesData = await ApiService.getSubstances();
      setSubstances(substancesData);
      setFilteredSubstances(substancesData); // Изначально фильтруем все вещества

      // Получаем типы веществ
      const typesData = await ApiService.getSubstanceTypes();
      
      // Проверка на дубли и сохранение уникальных типов веществ
      const uniqueTypes = typesData.filter((value, index, self) =>
        index === self.findIndex((t) => (
          t.id_Substance_type === value.id_Substance_type
        ))
      );

      setSubstanceTypes(uniqueTypes); // Получаем и сохраняем уникальные типы веществ
    } catch (error) {
      alert(error);
    }
  };
  useEffect(() => {
    fetchSubstancesAndTypes();
  }, []);

  const handleSearch = (e) => {
    const query = e.target.value.toLowerCase(); // Преобразуем запрос в нижний регистр
    setSearchQuery(query);

    filterData(query, minDensity, maxDensity, minCalorificValue, maxCalorificValue, selectedType);
  };
  const handleDeleteSelected = async () => {
    const selectedIds = Array.from(selectedRows); // Получаем все выбранные ID из Set
    const confirmation = window.confirm(`Вы уверены, что хотите удалить эти вещества под номерами "${selectedIds}"? Это действие нельзя будет отменить.`);
  
  if (confirmation) {
    try {
      const response = await ApiService.deleteSubstances(selectedIds); // Отправляем запрос на удаление
  
      if (response.message) {
        // Если сервер вернул сообщение
        alert(response.message); // Показываем сообщение в алерте
      }
  
      // Обновляем состояние с удаленными веществами
      setSelectedRows(new Set()); // Сбрасываем выбор
      fetchSubstancesAndTypes();
    } catch (error) {
      console.error('Ошибка при удалении:', error);
      alert('Произошла ошибка при удалении'); // Показываем сообщение об ошибке
    }
  }
  };

 // Открытие модального окна
 const handleShowEditModal  = (substance) => {
  setSubstanceToEdit(substance); // Передаем данные вещества для редактирования (если есть)
  setIsFormVisible(true); // Открываем форму
};
const handleShowAddModal  = () => {
  setSubstanceToEdit(null); // Передаем данные вещества для редактирования (если есть)
  setIsFormVisible(true); // Открываем форму
};
// Закрытие модального окна
const handleCloseEditModal = () => {
  setIsFormVisible(false); // Возврат к таблице
  fetchSubstancesAndTypes();
};

  // Функция для сортировки
  const handleSort = (key) => {
    let direction = 'asc';
    if (sortConfig.key === key && sortConfig.direction === 'asc') {
      direction = 'desc';
    }

    const sortedData = [...filteredSubstances].sort((a, b) => {
      if (a[key] < b[key]) {
        return direction === 'asc' ? -1 : 1;
      }
      if (a[key] > b[key]) {
        return direction === 'asc' ? 1 : -1;
      }
      return 0;
    });

    setFilteredSubstances(sortedData);
    setSortConfig({ key, direction });
  };
  const handleApplyFilters = () => {
    if(!filterEnabled&&!calorificFilterEnabled&&!typeFilterEnabled){
      fetchSubstancesAndTypes();
    }else{
    if (validateDensity(minDensity, maxDensity) && validateCalorific(minCalorificValue, maxCalorificValue)) {
      setErrorMessage(''); // Сбрасываем ошибку
      filterData(searchQuery, minDensity, maxDensity, minCalorificValue, maxCalorificValue, selectedType);
    } else {
      setErrorMessage('Проверьте правильность введенных данных.');
    }}
  };

  const validateDensity = (min, max) => {
    if (min < 0 || max < 0 || parseFloat(min) > parseFloat(max)) {
      return false;
    }
    return true;
  };

  const validateCalorific = (min, max) => {
    if (min < 0 || max < 0 || parseFloat(min) > parseFloat(max)) {
      return false;
    }
    return true;
  };

  const filterData = (query, minDensity, maxDensity, minCalorificValue, maxCalorificValue, selectedType) => {
    let filtered = substances.filter((substance) =>
      substance.name.toLowerCase().includes(query)
    );

    // Фильтрация по плотности, если фильтр по плотности активирован
    if (filterEnabled) {
      if (minDensity !== '' && !isNaN(minDensity)) {
        filtered = filtered.filter((substance) => substance.density >= parseFloat(minDensity));
      }
      if (maxDensity !== '' && !isNaN(maxDensity)) {
        filtered = filtered.filter((substance) => substance.density <= parseFloat(maxDensity));
      }
    }

    // Фильтрация по теплоте сгорания, если фильтр по калорийности активирован
    if (calorificFilterEnabled) {
      if (minCalorificValue !== '' && !isNaN(minCalorificValue)) {
        filtered = filtered.filter((substance) => substance.calorific_value >= parseFloat(minCalorificValue));
      }
      if (maxCalorificValue !== '' && !isNaN(maxCalorificValue)) {
        filtered = filtered.filter((substance) => substance.calorific_value <= parseFloat(maxCalorificValue));
      }
    }

    // Фильтрация по типу вещества, если фильтр по типу вещества активирован
    if (typeFilterEnabled && selectedType) {
      filtered = filtered.filter((substance) => substance.substance_type_name === selectedType);
    }

    setFilteredSubstances(filtered);
  };

  const indexOfLastRecord = currentPage * recordsPerPage;
  const indexOfFirstRecord = indexOfLastRecord - recordsPerPage;
  const currentRecords = filteredSubstances.slice(indexOfFirstRecord, indexOfLastRecord);

  const totalPages = Math.ceil(filteredSubstances.length / recordsPerPage);
  const pageNumbers = [];
  for (let i = 1; i <= totalPages; i++) {
    pageNumbers.push(i);
  }

  const handleSelectAll = () => {
    if (selectedRows.size === filteredSubstances.length) {
      setSelectedRows(new Set());
    } else {
      const allIds = new Set(filteredSubstances.map(substance => substance.id_Substance));
      setSelectedRows(allIds);
    }
  };

  const handleSelectRow = (id) => {
    setSelectedRows((prev) => {
      const updatedSelection = new Set(prev);
      if (updatedSelection.has(id)) {
        updatedSelection.delete(id);
      } else {
        updatedSelection.add(id);
      }
      return updatedSelection;
    });
  };

  return (
    <div className="table-container">
      {!isFormVisible ? (
        <div>
      {/* Строка поиска */}
      <Form.Group controlId="search" className="mb-3 search-form">
        <Form.Control
          type="search"
          placeholder="Введите название вещества"
          value={searchQuery}
          onChange={handleSearch}
          className="search-input"
        />
        <i className="fa fa-search search-icon"></i>
      </Form.Group>

      {/* Чекбокс для активации фильтра плотности */}
      <Form.Group controlId="filterEnabled" className="mb-3">
        <Form.Check
          type="checkbox"
          label="Активировать фильтр по плотности, кг/м³"
          checked={filterEnabled}
          onChange={(e) => setFilterEnabled(e.target.checked)}
        />
      </Form.Group>

      {/* Чекбокс для активации фильтра теплоты сгорания */}
      <Form.Group controlId="calorificFilterEnabled" className="mb-3">
        <Form.Check
          type="checkbox"
          label="Активировать фильтр по теплоте сгорания, Дж"
          checked={calorificFilterEnabled}
          onChange={(e) => setCalorificFilterEnabled(e.target.checked)}
        />
      </Form.Group>

      {/* Чекбокс для активации фильтра по типу вещества */}
      <Form.Group controlId="typeFilterEnabled" className="mb-3">
        <Form.Check
          type="checkbox"
          label="Активировать фильтр по типу вещества"
          checked={typeFilterEnabled}
          onChange={(e) => setTypeFilterEnabled(e.target.checked)}
        />
      </Form.Group>

      {/* Фильтры по плотности */}
      <div className="filters">
        {filterEnabled && (
          <div className="density-filters">
            <Form.Group controlId="minDensity" className="mb-3">
              <Form.Label>Минимальная плотность </Form.Label>
              <Form.Control
                type="number"
                value={minDensity}
                onChange={(e) => setMinDensity(e.target.value)}
                placeholder="Минимальная плотность"
              />
            </Form.Group>

            <Form.Group controlId="maxDensity" className="mb-3">
              <Form.Label>Максимальная плотность </Form.Label>
              <Form.Control
                type="number"
                value={maxDensity}
                onChange={(e) => setMaxDensity(e.target.value)}
                placeholder="Максимальная плотность"
              />
            </Form.Group>
          </div>
        )}

        {/* Фильтры по теплоте сгорания */}
        {calorificFilterEnabled && (
          <div className="calorific-filters">
            <Form.Group controlId="minCalorificValue" className="mb-3">
              <Form.Label>Минимальная теплота сгорания </Form.Label>
              <Form.Control
                type="number"
                value={minCalorificValue}
                onChange={(e) => setMinCalorificValue(e.target.value)}
                placeholder="Минимальная теплота"
              />
            </Form.Group>

            <Form.Group controlId="maxCalorificValue" className="mb-3">
              <Form.Label>Максимальная теплота сгорания </Form.Label>
              <Form.Control
                type="number"
                value={maxCalorificValue}
                onChange={(e) => setMaxCalorificValue(e.target.value)}
                placeholder="Максимальная теплота"
              />
            </Form.Group>
          </div>
        )}

        {/* Фильтр по типу вещества */}
        {typeFilterEnabled && (
          <div className="type-filter">
            <Form.Group controlId="substanceType" className="mb-3">
              <Form.Label>Тип вещества </Form.Label>
              <Form.Control
                as="select"
                value={selectedType}
                onChange={(e) => setSelectedType(e.target.value)}
              >
                <option value="">Выберите тип вещества</option>
                {substanceTypes.map((type) => (
                  <option key={type.id_Substance_type} value={type.s_t_name}>
                    {type.s_t_name}
                  </option>
                ))}
              </Form.Control>
            </Form.Group>
          </div>
        )}

        {/* Кнопка применения фильтров */}
        <button onClick={handleApplyFilters} className="btn btn-primary">
          Применить фильтр
        </button>

        {errorMessage && <p className="error-message">{errorMessage}</p>} {/* Ошибка фильтрации */}
      </div>
{/* Кнопка для удаления выбранных */}
{selectedRows.size > 0 && (
 <div className="delete-button-container">
 <Button
   variant="danger"
   className="delete-button"
   onClick={handleDeleteSelected}
   disabled={selectedRows.size === 0} // Кнопка отключена, если нет выбранных строк
 >
   Удалить
 </Button>
</div>
)}
      <Form.Group controlId="addSubstance" className="mb-3">
        <button className="add-substance-btn" onClick={handleShowAddModal}>
          Добавить вещество
        </button>
      </Form.Group>
 {/* Модальное окно для добавления вещества */}
      <Table className="substances-table" striped bordered hover responsive>
        <thead>
          <tr>
            <th>
              <input 
                type="checkbox" 
                checked={selectedRows.size === filteredSubstances.length} 
                onChange={handleSelectAll} 
              />
            </th>
            <th>№</th>
            <th onClick={() => handleSort('name')}>
            Название
              {sortConfig.key === 'name' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-down"></i>
                ) : (
                  <i className="fas fa-arrow-up"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
            <th onClick={() => handleSort('density')}>
              Плотность (кг/м³)
              {sortConfig.key === 'density' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
            <th onClick={() => handleSort('calorific_value')}>
              Теплота сгорания (Дж)
              {sortConfig.key === 'calorific_value' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
            <th onClick={() => handleSort('min_concentration')}>
            Минимальная концентрация (%)
              {sortConfig.key === 'min_concentration' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
            <th onClick={() => handleSort('max_concentration')}>
            Максимальная концентрация (%)
              {sortConfig.key === 'max_concentration' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
            <th onClick={() => handleSort('substance_type_name')}>
            Тип
              {sortConfig.key === 'substance_type_name' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-down"></i>
                ) : (
                  <i className="fas fa-arrow-up"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
            </th>
           
            <th onClick={() => handleSort('ip_address')}>
            IP адрес
              {sortConfig.key === 'ip_address' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
              </th>
            <th onClick={() => handleSort('redact_time')}>
            Время последнего редактирования
              {sortConfig.key === 'redact_time' ? (
                sortConfig.direction === 'asc' ? (
                  <i className="fas fa-arrow-up"></i>
                ) : (
                  <i className="fas fa-arrow-down"></i>
                )
              ) : (
                <i className="fas fa-sort"></i>
              )}
              </th>
              <th>Редактировать</th>
          </tr>
        </thead>
        <tbody>
          {currentRecords.map((substance) => (
            <tr key={substance.id_Substance} className={`table-row ${selectedRows.has(substance.id_Substance) ? 'selected' : ''}`}>
              <td>
                <input 
                  type="checkbox" 
                  checked={selectedRows.has(substance.id_Substance)} 
                  onChange={() => handleSelectRow(substance.id_Substance)} 
                />
              </td>
              <td>{substance.id_Substance}</td>
              <td>{substance.name}</td>
              <td>{substance.density}</td>
              <td>{substance.calorific_value}</td>
              <td>{substance.min_concentration}</td>
              <td>{substance.max_concentration}</td>
              <td>{substance.substance_type_name}</td>
              <td>{substance.ip_address}</td>
              <td>{new Date(substance.redact_time).toLocaleString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false 
                })}</td>
              <td>
              <Button 
  variant="link" 
  onClick={() => handleShowEditModal(substance)} 
  style={{ padding: 0, fontSize: '20px', color: '#007bff' }}>
  <i className="fas fa-pencil-alt"></i> {/* Иконка карандашика */}
</Button>
              </td>
            </tr>
          ))}
        </tbody>
      </Table>

      {/* Пагинация с иконками Font Awesome */}
      <div className="pagination-container">
        <div className="pagination-bar">
          <span 
            className="pagination-first" 
            onClick={() => setCurrentPage(1)}>
            <i className="fas fa-angle-double-left"></i>
          </span>
          
          <span 
            className="pagination-prev" 
            onClick={() => setCurrentPage(currentPage > 1 ? currentPage - 1 : 1)}>
            <i className="fas fa-angle-left"></i>
          </span>

          {pageNumbers.map((page) => (
            <span 
              key={page} 
              className={`pagination-item ${page === currentPage ? 'active' : ''}`} 
              onClick={() => setCurrentPage(page)}
            >
              {page}
            </span>
          ))}

          <span 
            className="pagination-next" 
            onClick={() => setCurrentPage(currentPage < totalPages ? currentPage + 1 : totalPages)}>
            <i className="fas fa-angle-right"></i>
          </span>
          
          <span 
            className="pagination-last" 
            onClick={() => setCurrentPage(totalPages)}>
            <i className="fas fa-angle-double-right"></i>
          </span>
        </div>
      </div>
    </div>
  ) : (
    <EditSubstanceModal show={showEditModal} handleClose={handleCloseEditModal}  substanceToEdit={substanceToEdit}/> 
      )}
  </div>
  );
};

export default SubstancesTable;

import config from '../config/Config.json';  // Импорт конфиг файла с URL API

class ApiService {
    static apiUrl = config.apiUrl; // Замените на реальный URL из конфигурации

    // Получение всех веществ
    static async getSubstances() {
      try {
        const response = await fetch(`${this.apiUrl}/substances?id=all`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        if (!response.ok) {
          const errorMessage = await this.extractErrorMessage(response);
          throw new Error(errorMessage);
        }

        const data = await response.json();
        return data;
      } catch (error) {
         throw error;
      }
    }

    // Получение всех типов веществ
    static async getSubstanceTypes() {
      try {
        const response = await fetch(`${this.apiUrl}/substance-types?id=all`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        if (!response.ok) {
          const errorMessage = await this.extractErrorMessage(response);
          throw new Error(errorMessage);
        }

        const data = await response.json();
        return data; // Возвращаем данные типов веществ
      } catch (error) {
        throw error;
      }
    }

    // Метод для извлечения сообщения об ошибке из ответа
    static async extractErrorMessage(response) {
      try {
        const errorData = await response.json();
        return errorData.error || `Код ответа от сервера: ${response.status}`;
      } catch (e) {
        return `Failed to parse error message from server (status ${response.status})`;
      }
    }
    // Удаление веществ по массиву ID
  static async deleteSubstances(ids) {
    try {
      const response = await fetch(`${this.apiUrl}/substances/delete`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(ids), // Массив с id веществ передаем напрямую
      });

      if (!response.ok) {
        const errorMessage = await this.extractErrorMessage(response);
        throw new Error(errorMessage);
      }

      const data = await response.json();
      return data;
    } catch (error) {
      throw error;
    }
  }
  static async addSubstanceType(substanceTypeData) {
    try {
      const response = await fetch(`${this.apiUrl}/substance-types/create`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(substanceTypeData),
      });
  
      if (!response.ok) {
        const errorMessage = await this.extractErrorMessage(response);
        throw new Error(errorMessage);
      }
  
      const data = await response.json();
      return data; // Возвращаем данные после успешного добавления
    } catch (error) {
      throw error; // Прокидываем ошибку дальше
    }
  }
  
  // Функция для добавления вещества
  static async addSubstance(substanceData) {
    try {
      const response = await fetch(`${this.apiUrl}/substances/create`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(substanceData),
      });

      if (!response.ok) {
        const errorMessage = await this.extractErrorMessage(response);
        throw new Error(errorMessage);
      }

      const data = await response.json();
      return data; // Возвращаем данные, если добавление прошло успешно
    } catch (error) {
     throw error; // Прокидываем ошибку дальше
    }
  }
  // Функция для обновления вещества
  static async updateSubstance(substanceId, substanceData) {
    try {
      const response = await fetch(`${this.apiUrl}/substances/update?id=${substanceId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(substanceData),
      });

      if (!response.ok) {
        const errorMessage = await this.extractErrorMessage(response);
        throw new Error(errorMessage);
      }

      const data = await response.json();
      return data; // Возвращаем данные, если обновление прошло успешно
    } catch (error) {
      throw error; // Прокидываем ошибку дальше
    }
  }
}

export default ApiService;

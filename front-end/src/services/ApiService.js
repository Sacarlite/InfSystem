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
        console.error('Error fetching substances:', error);
        throw error;
      }
    }

    // Получение вещества по ID
    static async getSubstanceById(id) {
      try {
        const response = await fetch(`${this.apiUrl}/substances?id=${id}`, {
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
        console.error(`Error fetching substance with ID ${id}:`, error);
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
        console.error('Error fetching substance types:', error);
        throw error;
      }
    }

    // Метод для извлечения сообщения об ошибке из ответа
    static async extractErrorMessage(response) {
      try {
        const errorData = await response.json();
        return errorData.error || `Server responded with status ${response.status}`;
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
      console.error('Error deleting substances:', error);
      throw error;
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
      console.error('Error adding substance:', error);
      throw error; // Прокидываем ошибку дальше
    }
  }
}

export default ApiService;

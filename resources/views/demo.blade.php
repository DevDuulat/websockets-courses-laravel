<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Демо: Категории</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .block {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        h1 { color: #333; }
        h2 { margin-top: 0; color: #444; }
        input, button {
            margin: 5px 0;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input { width: 200px; }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover { background-color: #45a049; }
        ul {
            padding-left: 0;
            list-style-type: none;
        }
        li {
            padding: 8px;
            margin: 5px 0;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<h1>Демо: Управление категориями</h1>

<div class="block">
    <h2>Создать категорию</h2>
    <input type="text" id="new-category-name" placeholder="Название категории" required>
    <button onclick="createCategory()">Создать</button>
    <p id="create-message" class="message"></p>
</div>

<div class="block">
    <h2>Список категорий</h2>
    <button onclick="fetchCategories()">Обновить список</button>
    <ul id="category-list"></ul>
</div>

<div class="block">
    <h2>Редактировать категорию</h2>
    <input type="number" id="edit-category-id" placeholder="ID" required>
    <input type="text" id="edit-category-name" placeholder="Новое название" required>
    <button onclick="updateCategory()">Обновить</button>
    <p id="update-message" class="message"></p>
</div>

<div class="block">
    <h2>Удалить категорию</h2>
    <input type="number" id="delete-category-id" placeholder="ID" required>
    <button onclick="deleteCategory()" class="delete-btn">Удалить</button>
    <p id="delete-message" class="message"></p>
</div>

<script>
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const API_URL = '/api/categories';

    // DOM элементы
    const elements = {
        newName: document.getElementById('new-category-name'),
        editId: document.getElementById('edit-category-id'),
        editName: document.getElementById('edit-category-name'),
        deleteId: document.getElementById('delete-category-id'),
        categoryList: document.getElementById('category-list'),
        messages: {
            create: document.getElementById('create-message'),
            update: document.getElementById('update-message'),
            delete: document.getElementById('delete-message')
        }
    };

    // Универсальная функция для обработки ошибок
    function handleError(error, messageElement) {
        console.error('Ошибка:', error);
        messageElement.textContent = 'Произошла ошибка: ' + (error.message || 'неизвестная ошибка');
        messageElement.className = 'message error';
        setTimeout(() => messageElement.textContent = '', 3000);
    }

    // Универсальная функция для отображения успешного сообщения
    function showSuccess(message, messageElement) {
        messageElement.textContent = message;
        messageElement.className = 'message success';
        setTimeout(() => messageElement.textContent = '', 3000);
    }

    // Создание категории
    async function createCategory() {
        if (!elements.newName.value) {
            alert('Пожалуйста, введите название категории');
            return;
        }

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ name: elements.newName.value })
            });

            if (!response.ok) throw new Error('Ошибка сервера');

            const data = await response.json();
            showSuccess(`Категория создана: ${data.name}`, elements.messages.create);
            elements.newName.value = '';
            fetchCategories();
        } catch (error) {
            handleError(error, elements.messages.create);
        }
    }

    // Получение списка категорий
    async function fetchCategories() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) throw new Error('Ошибка загрузки данных');

            const data = await response.json();
            elements.categoryList.innerHTML = '';

            if (data.length === 0) {
                elements.categoryList.innerHTML = '<li>Нет категорий</li>';
                return;
            }

            data.forEach(category => {
                const li = document.createElement('li');
                li.textContent = `ID: ${category.id} — ${category.name}`;
                elements.categoryList.appendChild(li);
            });
        } catch (error) {
            handleError(error, elements.messages.update);
        }
    }

    // Обновление категории
    async function updateCategory() {
        if (!elements.editId.value || !elements.editName.value) {
            alert('Пожалуйста, заполните все поля');
            return;
        }

        try {
            const response = await fetch(`${API_URL}/${elements.editId.value}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ name: elements.editName.value })
            });

            if (!response.ok) throw new Error('Ошибка обновления');

            const data = await response.json();
            showSuccess(`Категория обновлена: ${data.name}`, elements.messages.update);
            elements.editId.value = '';
            elements.editName.value = '';
            fetchCategories();
        } catch (error) {
            handleError(error, elements.messages.update);
        }
    }

    // Удаление категории
    async function deleteCategory() {
        if (!elements.deleteId.value) {
            alert('Пожалуйста, введите ID категории');
            return;
        }

        if (!confirm('Вы уверены, что хотите удалить эту категорию?')) return;

        try {
            const response = await fetch(`${API_URL}/${elements.deleteId.value}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            if (!response.ok) throw new Error('Ошибка удаления');

            showSuccess('Категория удалена', elements.messages.delete);
            elements.deleteId.value = '';
            fetchCategories();
        } catch (error) {
            handleError(error, elements.messages.delete);
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        fetchCategories();

        // Добавляем обработчики событий для полей ввода
        elements.newName.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') createCategory();
        });

        elements.editName.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') updateCategory();
        });
    });
</script>
<!-- Подключаем Pusher и Echo перед закрывающим тегом body -->
<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>

<script>
    // Твой существующий JS код...

    // Инициализация Pusher и Echo
    Pusher.logToConsole = true;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '8ae571f61ebaa59b0908', // из .env PUSHER_APP_KEY
        cluster: 'ap2',         // из .env PUSHER_APP_CLUSTER
        forceTLS: true
    });

    window.Echo.channel('categories')
        .listen('.category.created', (e) => {
            fetchCategories();
        })
        .listen('.category.updated', (e) => {
            fetchCategories();
        })
        .listen('.category.deleted', (e) => {
            fetchCategories();
        });
</script>

</body>
</html>

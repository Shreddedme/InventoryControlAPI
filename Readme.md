1. Скопировать проект с гитхаба
    git clone https://github.com/Shreddedme/InventoryControlAPI.git
2. Собираем и поднимаем проект с помощью docker compose
    docker-compose up --build
 
   Зайти в php контейнер
   docker exec -it php bash

    Чтобы запустить тесты
    в контейнере прописываем php bin/phpunit

3. Для демонстрации API методов
    в корне проекта предоставлена коллекция запросов (Warehouse.postman_collection.json)
    которую можно использовать с помощью Postman или другого
   инструмента который поддерживает импорт коллекций в формате json.
    
    Для импорта коллекции открыть Postman, нажать на кнопку Import,
    выбрать файл с коллекцией запросов (Warehouse.postman_collection.json)
    После импорта коллекция запросов появится на панели Postman

У цьому проєкті використовуються: Laravel 12 + Sail, MySQL 8.3, Orchid для адмінки. 

Для встановлення проєкту:
    
    1. Клонувати проект з репозиторію.
    2. Встановлюємо пакети:
        composer install
    3. Створюємо і запускаємо сервіси:
        ./vendor/bin/sail up -d  
        або виконати:
            alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
        тоді можна буде використовувати ось так:
            sail up -d
    4. Виконати міграції для бази данних:
        ./vendor/bin/sail artisan migrate
        aбо 
        sail artisan migrate
    5. Створити адміністратора в Orchid(редагуємо пошту і пароль):
        ./vendor/bin/sail artisan orchid:admin admin@example.com password
        або
        sail artisan orchid:admin admin@example.com password
    Можна працювати.

    Доступ до проєкту
    Сайт: http://localhost
    Панель адміністратора (Orchid): http://localhost/admin

    Корисні команди:
    # Зайти в контейнер
    ./vendor/bin/sail shell
    або
    sail shell

    # Очистити кеші
    ./vendor/bin/sail artisan optimize:clear
    або
    sail artisan optimize:clear

    # Список доступних команд Sail
    ./vendor/bin/sail
    або
    sail


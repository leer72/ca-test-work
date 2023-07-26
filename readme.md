# Запуск проекта
1. Установить пакеты: composer install
2. Создать БД: php bin/console doctrine:database:create
3. Выполнить миграции: php bin/console doctrine:migrations:migrate
4. Запустить consumer: php bin/console rabbitmq:consumer calculator -vvv

# Запуск тестов
1. php bin/phpunit tests/
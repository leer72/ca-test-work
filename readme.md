# Запуск проекта
1. docker-compose build
2. docker-compose --env-file ./../.env up -d
3. Установить пакеты: composer install
4. Создать БД: php bin/console doctrine:database:create
5. Выполнить миграции: php bin/console doctrine:migrations:migrate
6. Запустить consumer: php bin/console rabbitmq:consumer calculator -vvv

# Запуск тестов
1. php bin/phpunit tests/
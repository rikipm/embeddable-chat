# Развёртывание приложения

1. Склонируйте проект.
2. `cp compose.example.yaml compose.yaml`
3. (Опционально) Если ваши UID:GID отличается от 1000:1000 то в `compose.yaml:services>php>build>docker>args` замените
   их на свои. Это необходимо
   из-за сложностей с volume докера и пермишеннами.
4. `cp src/.env.example src/.env`
5. Сгенерируйте пароль для БД (например с помощью `openssl rand -base64 12`). Установите его в
   `compose.yaml:services>postgres>environment>POSTGRES_PASSWORD` и `src/.env:DB_PASSWORD`
6. Установите ключ OpenRouter в `src/.env:OPENROUTER_API_KEY`
7. `docker compose up`
8. `docker compose exec php composer install`
9. `docker compose exec php php artisan key:generate`
10. `docker compose exec php php artisan migrate`
11. `docker compose exec php npm install`
12. `docker compose exec php npm run build`
13. Для HMR: `docker compose exec php npm run dev`

# Запуск тестов

`docker compose exec php php artisan test`

Тесты проводятся в in-memory sqlite. В реальном проекте я бы создал вторую БД postgres.

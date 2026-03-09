# API сервиса управления заказами

Тестовое задание: backend-сервис для управления заказами интернет-магазина запчастей.

**Стек:** PHP 8.4+, Laravel 12, PostgreSQL, Redis, Docker

## Быстрый старт

### 1. Поднять проект

```bash
docker compose up -d
```

### 2. Настроить окружение

Скопировать переменные для Docker

```bash
cp .env.docker.example .env
```

Установить зависимости и сгенерировать ключ:

```bash
docker compose exec app composer i

docker compose exec app php artisan key:generate
```

### 3. Миграции и сидеры

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

### 4. Запуск очереди

```bash
docker compose exec app php artisan queue:work
```

### 5. Запуск тестов

```bash
docker compose exec app php artisan test
```

Либо локально (при установленном PHP и зависимостях):

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan test
```

## API

Базовый URL: `http://localhost:8080/api/v1`

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /products | Список товаров. Параметры: `category`, `search`, `per_page`, `page` |
| POST | /orders | Создание заказа. Body: `{ "customer_id": 1, "items": [{ "product_id": 1, "quantity": 2 }] }` |
| GET | /orders | Список заказов. Параметры: `status`, `customer_id`, `date_from`, `date_to`, `per_page`, `page` |
| GET | /orders/{id} | Детали заказа |
| PATCH | /orders/{id}/status | Смена статуса. Body: `{ "status": "confirmed" }` |

**Статусы заказа:** `new` → `confirmed` → `processing` → `shipped` → `completed`. Переход в `cancelled` из `new` и `confirmed`.

**Rate limiting:** не более 10 POST /orders в минуту на IP.

## Swagger / OpenAPI

Документация доступна по адресу:

```
http://localhost:8080/api/documentation
```

Генерация OpenAPI-спека:

```bash
php artisan l5-swagger:generate
```

## Структура

- **Модели:** Product, Customer, Order, OrderItem, OrderExport
- **Сервисы:** `Application\Services\Order\Order\OrderService` (сценарии заказов), `ProductCacheService` (кеш товаров в Redis)
- **События:** OrderStatusChanged → ExportOrderListener → ExportOrderJob (HTTP на httpbin.org)
# demo-order-manage

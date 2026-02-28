<<<<<<< HEAD
# Real Estate Management System

Система управления недвижимостью на Laravel 11 + Orchid Platform. Управление жилыми комплексами, зданиями, секциями, этажами и помещениями с админ-панелью, историей изменений и REST API.

## Требования

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3
- Node.js 18+ (для Vite, если используется)
- Redis (опционально, для cache tags)

## Установка

### 1. Клонирование и зависимости

```bash
git clone <repo-url> real-estate && cd real-estate
composer install
```

### 2. Конфигурация окружения

```bash
cp .env.example .env
php artisan key:generate
```

Отредактируйте `.env`:

```env
# База данных (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=real_estate
DB_USERNAME=root
DB_PASSWORD=

# URL приложения (для Orchid)
APP_URL=http://localhost:8000

# Кэш (опционально, для tags)
CACHE_STORE=database
# CACHE_STORE=redis
# REDIS_HOST=127.0.0.1
```

### 3. База данных и хранилище

```bash
php artisan migrate
php artisan storage:link
```

> **Важно:** `storage:link` создаёт симлинк `public/storage` → `storage/app/public`. Без него загрузка изображений в Orchid не работает.

### 4. Orchid Admin

```bash
php artisan orchid:admin
```

Введите имя, email и пароль. Вход: **http://localhost:8000/admin**

### 5. Демо-данные (опционально)

```bash
php artisan db:seed --class=RealEstateSeeder
```

Создаёт 3 комплекса с полной иерархией и ~50–700 помещений.
=======
# real-estate
>>>>>>> 9cd1dbf37cdd34a79c8db61929bde5bccc9f135b

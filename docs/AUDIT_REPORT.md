# Real Estate Management System — Audit Report

## 1. Project Overview

**Стек:** Laravel 11.48, PHP 8.2+, Orchid Platform 14.52, MySQL/PostgreSQL/SQLite.

**Запуск:**
- `composer install` → `cp .env.example .env` → `php artisan key:generate`
- `php artisan migrate` (DB: MySQL/Postgres в .env)
- `php artisan storage:link`
- `php artisan orchid:admin` — создать админа
- `php artisan serve` → http://localhost:8000/admin

**Структура:**
- Модели: `app/Models/` (Complex, Building, Section, Floor, Premise, PremiseStatusHistory, PremisePriceHistory, AuditLog)
- Enums: `app/Enums/` (ComplexStatus, PremiseType, PremiseStatus)
- Orchid: `app/Orchid/Screens/`, `Layouts/`, `Filters/`
- Observers: `app/Observers/` (PremiseObserver, AuditObserver, CacheInvalidationObserver)
- API: `app/Http/Controllers/Api/` (PremiseController, StatsController)
- Кэш: `app/Services/RealEstateCacheService.php`

---

## 2. Feature Checklist vs ТЗ

| ТЗ | Статус | Файлы/примечания |
|----|--------|------------------|
| **A) Модели и связи** |
| Complex: name, description, address, status, gallery, lat/lng | ✅ | `complexes` migration, `app/Models/Complex.php` |
| Building belongsTo Complex | ✅ | `buildings.complex_id` FK, `app/Models/Building.php` |
| Section belongsTo Building | ✅ | `sections.building_id` FK |
| Floor belongsTo Section | ✅ | `floors.section_id` FK. **Не Building** — по ТЗ "или напрямую зданиям" выбрано Section |
| Premise belongsTo Floor | ✅ | `premises.floor_id` FK |
| Premise: apartment_number, type, rooms, areas, status, prices, floor_number, layout_image, gallery, extras | ✅ | `premises` migration, `app/Models/Premise.php` |
| **B) Orchid Screens** |
| ComplexListScreen | ✅ | `app/Orchid/Screens/Complex/ComplexListScreen.php` |
| ComplexEditScreen | ✅ | `app/Orchid/Screens/Complex/ComplexEditScreen.php` |
| BuildingListScreen | ✅ | `app/Orchid/Screens/Building/BuildingListScreen.php` |
| BuildingEditScreen | ✅ | `app/Orchid/Screens/Building/BuildingEditScreen.php` |
| SectionListScreen | ✅ | `app/Orchid/Screens/Section/SectionListScreen.php` |
| SectionEditScreen | ✅ | `app/Orchid/Screens/Section/SectionEditScreen.php` |
| FloorListScreen | ✅ | `app/Orchid/Screens/Floor/FloorListScreen.php` |
| FloorEditScreen | ✅ | `app/Orchid/Screens/Floor/FloorEditScreen.php` |
| PremiseListScreen | ✅ | `app/Orchid/Screens/Premise/PremiseListScreen.php` |
| PremiseEditScreen | ✅ | `app/Orchid/Screens/Premise/PremiseEditScreen.php` |
| DashboardScreen | ✅ | `app/Orchid/Screens/Dashboard/DashboardScreen.php` |
| PremiseStatusHistoryScreen | ✅ | `app/Orchid/Screens/Premise/PremiseStatusHistoryScreen.php` |
| PremisePriceHistoryScreen | ✅ | `app/Orchid/Screens/Premise/PremisePriceHistoryScreen.php` |
| **Фильтры** |
| ComplexListScreen: фильтр по статусу, поиск | ✅ | `ComplexStatusFilter`, TD::filter() на name/address |
| BuildingListScreen: фильтр по комплексу | 🟡 | Через query param `?complex=`, не Orchid Filter |
| SectionListScreen: фильтр по зданию | 🟡 | Через query param `?building=` |
| FloorListScreen: фильтр по секции | 🟡 | Через query param `?section=` |
| PremiseListScreen: все фильтры (комплекс, здание, секция, этаж, тип, статус, комнаты, цена, площадь) | ✅ | `app/Orchid/Filters/Premise/*.php` |
| **Charts/Widgets** |
| Dashboard: метрики, графики, топ-10, последние изменения | ✅ | `DashboardScreen`, `SalesChartLayout`, `ComplexStatusChartLayout` |
| **C) История и audit** |
| История статусов помещений | ✅ | `PremiseObserver`, `premise_status_history` |
| История цен | ✅ | `PremiseObserver`, `premise_price_history` |
| Audit log (user, time) | ✅ | `AuditObserver`, `audit_logs` |
| **D) Кэш** |
| Кэш списков комплексов/зданий/секций | ✅ | `RealEstateCacheService` |
| Кэш дашборда TTL 15–30 мин | ✅ | TTL 15 min для dashboard, 30 min для списков |
| Cache tags + инвалидация | 🟡 | Tags только при Redis; fallback без тегов (database/file) |
| Кэш результатов фильтрации | ❌ | Не реализовано |
| **E) Seed/Test** |
| Factories | ✅ | `database/factories/*Factory.php` |
| Seeder: 2–3 комплекса, 50–100 помещений | ✅ | `RealEstateSeeder` (создаёт ~686 помещений) |
| PHPUnit тесты | ❌ | Только ExampleTest, нет тестов домена |
| **F) Изображения** |
| Complex: gallery | ✅ | Orchid Attachments (complex_gallery), sync в save() |
| Floor: plan_image | ✅ | Orchid Attachments (floor_plan), sync в save() |
| Premise: layout_image + gallery | ✅ | Orchid Attachments (premise_layout, premise_gallery), sync в save() |

---

## 3. Data Model & DB

### Таблицы

| Таблица | Поля | Индексы | FK |
|--------|------|---------|-----|
| `complexes` | id, name, description, address, status, gallery (json), lat, lng, timestamps | status | — |
| `buildings` | id, complex_id, name, number, floors_count, built_year, timestamps | complex_id | complex_id → complexes (cascade) |
| `sections` | id, building_id, name, number, floors_count_in_section, timestamps | building_id | building_id → buildings (cascade) |
| `floors` | id, section_id, number, apartments_count, plan_image, timestamps | section_id | section_id → sections (cascade) |
| `premises` | id, floor_id, apartment_number, type, rooms, area_*, status, price_*, floor_number, layout_image, gallery, extras, timestamps | floor_id, type, status, (type,status) | floor_id → floors (cascade) |
| `premise_status_history` | id, premise_id, old_status, new_status, changed_by, changed_at, timestamps | premise_id+changed_at | premise_id (cascade), changed_by → users (nullOnDelete) |
| `premise_price_history` | id, premise_id, old_price, new_price, changed_by, changed_at, timestamps | premise_id+changed_at | premise_id (cascade), changed_by → users |
| `audit_logs` | id, auditable_type, auditable_id, user_id, action, old_values, new_values, timestamps | auditable_*, user_id+created_at | user_id (nullOnDelete) |

### Enums (PHP 8.2)

- `ComplexStatus`: planning, construction, completed
- `PremiseType`: apartment, studio, penthouse, commercial
- `PremiseStatus`: available, reserved, sold, not_for_sale

### Casts

- Complex: status → enum, gallery → array, lat/lng → float
- Premise: type, status → enum, areas/prices → float, gallery/extras → array

---

## 4. Orchid Admin

### Screens

| Screen | Route | Layouts | Примечания |
|--------|-------|---------|------------|
| DashboardScreen | `/admin/main` (platform.main) | metrics, SalesChartLayout, ComplexStatusChartLayout, tables | Работает |
| ComplexListScreen | `/admin/complexes` | ComplexFiltersLayout, ComplexListLayout | Фильтр статуса, поиск по name/address |
| ComplexEditScreen | `/admin/complexes/create`, `/admin/complexes/{id}/edit` | ComplexEditLayout | Gallery сохраняется через Orchid Attachments (sync) |
| BuildingListScreen | `/admin/buildings` | BuildingListLayout | Фильтр ?complex= в query |
| BuildingEditScreen | `/admin/buildings/create`, `/{id}/edit` | BuildingEditLayout | Relation для complex_id |
| SectionListScreen | `/admin/sections` | SectionListLayout | Фильтр ?building= |
| SectionEditScreen | `/admin/sections/create`, `/{id}/edit` | SectionEditLayout | — |
| FloorListScreen | `/admin/floors` | FloorListLayout | Фильтр ?section= |
| FloorEditScreen | `/admin/floors/create`, `/{id}/edit` | FloorEditLayout | plan_image сохраняется через Orchid Attachments (sync) |
| PremiseListScreen | `/admin/premises` | PremiseFiltersLayout, PremiseListLayout | 9 фильтров |
| PremiseEditScreen | `/admin/premises/create`, `/{id}/edit` | PremiseEditLayout | layout_image, gallery через Orchid Attachments; extras (balcony, loggia, view, parking) |
| PremiseStatusHistoryScreen | `/admin/premises/status-history`, `/{premise}` | Table | — |
| PremisePriceHistoryScreen | `/admin/premises/price-history`, `/{premise}` | Table | — |

### Изображения (исправлено)

1. **Orchid Attachments:** Модели Complex, Floor, Premise используют trait `Attachable`, accessors возвращают ID вложений для форм. В save() выполняется `sync()` по группам (`complex_gallery`, `floor_plan`, `premise_layout`, `premise_gallery`). Legacy-колонки в БД оставлены, но не используются.

---

## 5. Change History & Audit

### Механизм

- **Observers:** `app/Observers/PremiseObserver.php`, `app/Observers/AuditObserver.php`
- **Регистрация:** `app/Providers/AppServiceProvider.php` → `observe()`

### PremiseObserver (updating)

- При смене `status` → запись в `premise_status_history` (old_status, new_status, changed_by, changed_at)
- При смене `price_base`/`price_discount` → запись в `premise_price_history` (old_price, new_price, changed_by, changed_at)

### AuditObserver (created, updated, deleted)

- Модели: Complex, Building, Section, Floor, Premise
- Пишет в `audit_logs`: auditable_type, auditable_id, user_id, action, old_values, new_values

### Ограничения

- `changed_by` = `Auth::id()` — при сидере/консоли будет null
- Нет логирования при массовых операциях (если появятся)

---

## 6. Caching & Performance

### RealEstateCacheService

| Метод | Ключ | TTL | Tags (Redis) |
|-------|------|-----|---------------|
| getComplexesList() | real_estate:complexes:list | 30 min | real_estate:complexes |
| getBuildingsList(id) | real_estate:buildings:list:{id} | 30 min | real_estate:buildings |
| getSectionsList(id) | real_estate:sections:list:{id} | 30 min | real_estate:sections |
| getDashboardStats() | real_estate:dashboard:stats | 15 min | real_estate:dashboard |

### Инвалидация

- `CacheInvalidationObserver` на saved/deleted для Complex, Building, Section, Floor, Premise
- Без Redis: `Cache::forget()` по ключам; при invalidateBuildings/Sections(null) — перебор всех ID

### Проблемы

1. **database/file cache:** теги не поддерживаются, используется fallback.
2. **Кэш фильтрации Premise:** не реализован.
3. **Dashboard:** использует `getDashboardStats()` из кэша, но `complexStats` и `salesByMonth` считаются каждый раз (не кэшируются).

---

## 7. Gaps & Recommendations

### P0 (критично) — выполнено

| # | Задача | Статус |
|---|--------|--------|
| 1 | Сохранение gallery в ComplexEditScreen | ✅ Attachable + sync в save() |
| 2 | Синхронизация plan_image (Floor), layout_image/gallery (Premise) с Orchid Attachment | ✅ Attachable + sync во всех EditScreen |

### P1 (важно)

| # | Задача | Файлы |
|---|--------|-------|
| 3 | Кэшировать complexStats и salesByMonth в Dashboard | `DashboardScreen.php`, `RealEstateCacheService.php` |
| 4 | Добавить PHPUnit тесты (модели, scopes, API) | `tests/Unit/`, `tests/Feature/` |
| 5 | Добавить `php artisan storage:link` в инструкцию установки | README |
| 6 | Docker/Sail: docker-compose.yml для MySQL+Redis | корень проекта |

### P2 (желательно)

| # | Задача |
|---|--------|
| 7 | Orchid Filters для BuildingList (по комплексу), SectionList (по зданию), FloorList (по секции) |
| 8 | Кэш результатов фильтрации Premise (по hash параметров) |
| 9 | API: аутентификация (sanctum) для защищённых эндпоинтов |
| 10 | Локализация (ru/en) для Orchid |

### Риски

- **Изображения:** ✅ Исправлено — gallery/plan/layout сохраняются через Orchid Attachments.
- **Кэш без Redis:** при большом числе комплексов/зданий `invalidateBuildings()`/`invalidateSections()` без тегов делает много `Cache::forget()` — средний риск.
- **Тесты:** отсутствие тестов усложняет рефакторинг — средний риск.
<!--  -->
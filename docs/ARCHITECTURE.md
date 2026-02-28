# Real Estate Management System — Architecture

## 1. Architecture

### Сущности и связи

```
Complex (1) ──► (N) Building
Building (1) ──► (N) Section
Section (1) ──► (N) Floor
Floor (1) ──► (N) Premise
```

**Решение по Floor:** Floor принадлежит Section (не Building). Причина: в жилых комплексах секции часто имеют разное количество этажей; этаж логически входит в секцию. При необходимости можно добавить `building_id` для денормализации/навигации.

### Обязательные / опциональные части

| Часть | Обязательно | Опционально |
|-------|-------------|-------------|
| Complex/Building/Section/Floor/Premise CRUD | ✓ | |
| Фильтры Premise (комплекс, здание, секция, этаж, тип, статус, комнаты, цена, площадь) | ✓ | |
| История статусов/цен + audit log | ✓ | |
| Dashboard + виджеты/чарты | ✓ | |
| Кэширование (списки, stats, TTL 15–30 min) | ✓ | |
| REST API | | ✓ |

### Стратегия хранения изображений

- **Диск:** `storage/app/public/real-estate/`
- **Структура:**
  - `complexes/{id}/gallery/` — галерея комплекса
  - `floors/{id}/plan/` — план этажа (один файл)
  - `premises/{id}/layout/` — планировка помещения
  - `premises/{id}/gallery/` — галерея помещения
- **В БД:** пути вида `real-estate/complexes/1/gallery/image.jpg` (относительно `storage/app/public`)
- **Ссылка:** `Storage::url($path)` → `/storage/real-estate/...`

---

## 2. Database Schema

### Таблицы

| Таблица | Ключевые поля |
|---------|---------------|
| `complexes` | id, name, description, address, status, lat, lng, gallery (json), timestamps |
| `buildings` | id, complex_id (FK), name, number, floors_count, built_year, timestamps |
| `sections` | id, building_id (FK), name, number, floors_count_in_section, timestamps |
| `floors` | id, section_id (FK), number, apartments_count, plan_image, timestamps |
| `premises` | id, floor_id (FK), apartment_number, type, rooms, area_total, area_living, area_kitchen, status, price_base, price_discount, price_per_m2, floor_number, layout_image, gallery (json), extras (json), timestamps |
| `premise_status_history` | id, premise_id (FK), old_status, new_status, changed_by (user_id), changed_at |
| `premise_price_history` | id, premise_id (FK), old_price, new_price, changed_by, changed_at |
| `audit_logs` | id, auditable_type, auditable_id, user_id, action, old_values (json), new_values (json), created_at |

### Индексы и FK

- Все `*_id` — FK с `onDelete('cascade')` для каскадного удаления
- Индексы: `complexes.status`, `premises.type`, `premises.status`, `premises.floor_id`, `premises.complex_id` (через floor→section→building→complex или денормализация)

---

## 3. Enums (PHP 8.2)

- `ComplexStatus`: planning, construction, completed
- `PremiseType`: apartment, studio, penthouse, commercial
- `PremiseStatus`: available, reserved, sold, not_for_sale

---

## 4. Implementation Plan

| Итерация | Содержание | Файлы/команды |
|----------|------------|----------------|
| **I1** | БД + модели + сидер/фабрики | migrations, models, factories, seeders |
| **I2** | Orchid CRUD: Complex, Building, Section, Floor | Screens, Layouts, PlatformProvider menu |
| **I3** | Premise CRUD + расширенные фильтры | PremiseListScreen, PremiseEditScreen, Filters |
| **I4** | History (status/price) + audit log | Observers, premise_status_history, premise_price_history, audit_logs |
| **I5** | Dashboard + кэш | Widgets, CacheService, TTL 15–30 min |
| **I6** (опц.) | REST API | ApiController, routes/api.php |

---

## 5. Caching Strategy

| Ключ | TTL | Tags | Инвалидация |
|------|-----|------|-------------|
| `real_estate:complexes:list` | 30 min | complexes | при create/update/delete Complex |
| `real_estate:buildings:list:{complex_id}` | 30 min | buildings, complexes | при изменении Building/Complex |
| `real_estate:sections:list:{building_id}` | 30 min | sections, buildings | при изменении Section/Building |
| `real_estate:dashboard:stats` | 15 min | dashboard, premises | при изменении Premise |
| `real_estate:filter:{hash}` | 5 min | premises | при изменении Premise (опц.) |

---

## 6. Testing & Seed

- Factories: ComplexFactory, BuildingFactory, SectionFactory, FloorFactory, PremiseFactory
- Seeder: 2–3 комплекса, полная иерархия, 50–100 помещений
- `php artisan db:seed --class=RealEstateSeeder`
- `php artisan db:seed` — полный сид

## 7. Команды

```bash
# MySQL (в .env: DB_CONNECTION=mysql)
composer require orchid/platform
php artisan orchid:install
php artisan orchid:admin

# Миграции
php artisan migrate

# Сидер
php artisan db:seed --class=RealEstateSeeder

# Кэш (требует Redis для tags)
# CACHE_STORE=redis в .env
```

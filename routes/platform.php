<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main (Dashboard)
Route::screen('/main', \App\Orchid\Screens\Dashboard\DashboardScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Real Estate
Route::screen('complexes', \App\Orchid\Screens\Complex\ComplexListScreen::class)
    ->name('platform.complexes')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Комплексы', route('platform.complexes')));

Route::screen('complexes/create', \App\Orchid\Screens\Complex\ComplexEditScreen::class)
    ->name('platform.complexes.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.complexes')->push('Создать', route('platform.complexes.create')));

Route::screen('complexes/{complex}/edit', \App\Orchid\Screens\Complex\ComplexEditScreen::class)
    ->name('platform.complexes.edit')
    ->breadcrumbs(fn (Trail $trail, $complex) => $trail->parent('platform.complexes')->push($complex->name, route('platform.complexes.edit', $complex)));

Route::screen('buildings', \App\Orchid\Screens\Building\BuildingListScreen::class)
    ->name('platform.buildings')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Здания', route('platform.buildings')));

Route::screen('buildings/create', \App\Orchid\Screens\Building\BuildingEditScreen::class)
    ->name('platform.buildings.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.buildings')->push('Создать', route('platform.buildings.create')));

Route::screen('buildings/{building}/edit', \App\Orchid\Screens\Building\BuildingEditScreen::class)
    ->name('platform.buildings.edit')
    ->breadcrumbs(fn (Trail $trail, $building) => $trail->parent('platform.buildings')->push($building->name, route('platform.buildings.edit', $building)));

Route::screen('sections', \App\Orchid\Screens\Section\SectionListScreen::class)
    ->name('platform.sections')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Секции', route('platform.sections')));

Route::screen('sections/create', \App\Orchid\Screens\Section\SectionEditScreen::class)
    ->name('platform.sections.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.sections')->push('Создать', route('platform.sections.create')));

Route::screen('sections/{section}/edit', \App\Orchid\Screens\Section\SectionEditScreen::class)
    ->name('platform.sections.edit')
    ->breadcrumbs(fn (Trail $trail, $section) => $trail->parent('platform.sections')->push($section->name, route('platform.sections.edit', $section)));

Route::screen('floors', \App\Orchid\Screens\Floor\FloorListScreen::class)
    ->name('platform.floors')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Этажи', route('platform.floors')));

Route::screen('floors/create', \App\Orchid\Screens\Floor\FloorEditScreen::class)
    ->name('platform.floors.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.floors')->push('Создать', route('platform.floors.create')));

Route::screen('floors/{floor}/edit', \App\Orchid\Screens\Floor\FloorEditScreen::class)
    ->name('platform.floors.edit')
    ->breadcrumbs(fn (Trail $trail, $floor) => $trail->parent('platform.floors')->push('Этаж ' . $floor->number, route('platform.floors.edit', $floor)));

Route::screen('premises', \App\Orchid\Screens\Premise\PremiseListScreen::class)
    ->name('platform.premises')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Помещения', route('platform.premises')));

Route::screen('premises/status-history', \App\Orchid\Screens\Premise\PremiseStatusHistoryScreen::class)
    ->name('platform.premises.status-history')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.premises')->push('История статусов', route('platform.premises.status-history')));

Route::screen('premises/status-history/{premise}', \App\Orchid\Screens\Premise\PremiseStatusHistoryScreen::class)
    ->name('platform.premises.status-history.premise')
    ->breadcrumbs(fn (Trail $trail, $premise) => $trail->parent('platform.premises')->push('История: ' . $premise->apartment_number, route('platform.premises.status-history.premise', $premise)));

Route::screen('premises/price-history', \App\Orchid\Screens\Premise\PremisePriceHistoryScreen::class)
    ->name('platform.premises.price-history')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.premises')->push('История цен', route('platform.premises.price-history')));

Route::screen('premises/price-history/{premise}', \App\Orchid\Screens\Premise\PremisePriceHistoryScreen::class)
    ->name('platform.premises.price-history.premise')
    ->breadcrumbs(fn (Trail $trail, $premise) => $trail->parent('platform.premises')->push('История цен: ' . $premise->apartment_number, route('platform.premises.price-history.premise', $premise)));

Route::screen('premises/create', \App\Orchid\Screens\Premise\PremiseEditScreen::class)
    ->name('platform.premises.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.premises')->push('Создать', route('platform.premises.create')));

Route::screen('premises/{premise}/edit', \App\Orchid\Screens\Premise\PremiseEditScreen::class)
    ->name('platform.premises.edit')
    ->breadcrumbs(fn (Trail $trail, $premise) => $trail->parent('platform.premises')->push($premise->apartment_number, route('platform.premises.edit', $premise)));

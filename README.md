# lumen-recipes
Простое апи для работы с рецептами. Based on Lumen.

**Опубликовано для портфолио. Не предназачено для реальной работы.**

# Установка

- Залить на сервер.
- Указать доступ к БД, адрес сайта в .env.
- Запустить `composer install`.
- Запустить `php artisan migrate`.

# Точки

Наличие `auth` указывает на то, что запрос требует указания токена пользователя (заголовок Token, получать при создании пользователя).

## Пользователи

**POST /users** - создание пользователя (UsersController@store).

**GET /me** auth - информация о текущем пользователе (UsersController@me).

## Рецепты

**GET /recipes** auth - все созданные пользователем рецепты (15 на страницу) (RecipesController@index).

**POST /recipes** auth - добавление нового рецепта (RecipesController@store).

**GET /recipes/{id}** auth - информация о рецепте (RecipesController@show).

**PUT /recipes/{id}** auth - обновление рецепта (RecipesController@update).

**DELETE /recipes/{id}** auth - удаление рецепта (RecipesController@destroy).

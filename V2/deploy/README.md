# Установка стенда v2.tenirhotels.com (Plesk, shared-хостинг)

Схема: GitHub Actions собирает сайт (Composer + Vite) и кладёт готовый результат в ветку **`v2-deploy`**.
Plesk тянет эту ветку и копирует в папку сайта. Composer и Node на хостинге не нужны.

## 1. Хостинг (один раз)
1. **Домены → v2.tenirhotels.com → Настройки PHP**: версия 8.3 (минимум 8.2), расширения `intl`, `mbstring`, `pdo_mysql`, `fileinfo`, `gd`, `zip`; `memory_limit` ≥ 256M.
2. **Базы данных → Добавить базу**: запишите имя, пользователя, пароль.
3. **Git → Добавить репозиторий → Удалённый репозиторий**:
   - URL: `git@github.com:tenirhotels/website.git` (для приватного репозитория).
   - Plesk покажет публичный SSH-ключ. Добавьте его на GitHub: репозиторий → Settings → Deploy keys → Add (только чтение).
   - Ветка: `v2-deploy`. Режим: «Автоматически» (webhook) или «Вручную».
   - Путь деплоя: `/httpdocs` (или подпапка, например `/v2`).
   - Команды после деплоя (поле «Дополнительные действия»):
     ```
     test -f .env && /opt/plesk/php/8.3/bin/php artisan migrate --force
     test -f .env && /opt/plesk/php/8.3/bin/php artisan optimize
     ```
4. **Хостинг и DNS → Корневой каталог документов**: `httpdocs/public` (если путь деплоя другой, то `<путь>/public`).
   Это обязательно: наружу должна смотреть только папка `public`.
5. **Файлы**: в корне сайта (рядом с `artisan`) создайте `.env` по шаблону `deploy/env.stage.example` (подставьте базу).

## 2. Первый запуск
Нажмите «Получить обновления и развернуть». Затем выполните команды (по SSH, либо через **Запланированные задания → Запустить команду**, один раз, потом удалить):
```
cd <путь к сайту>
/opt/plesk/php/8.3/bin/php artisan key:generate --force
/opt/plesk/php/8.3/bin/php artisan migrate --seed --force
/opt/plesk/php/8.3/bin/php artisan storage:link
/opt/plesk/php/8.3/bin/php artisan make:filament-user --name="Nurzhan" --email="nurzhanadiluly@gmail.com" --password="ПАРОЛЬ"
/opt/plesk/php/8.3/bin/php artisan optimize
```
`--seed` выполняется **только один раз**: иначе контент задвоится.

## 3. Проверка
- `https://v2.tenirhotels.com/ru`: сайт.
- `https://v2.tenirhotels.com/admin`: админка.
- Закройте стенд от поисковиков: **Защищённые паролем каталоги** → `/` (или `public`).

## 4. Обновления
Любой `push` в `v2-skeleton` запускает сборку (GitHub → Actions → «Build V2 for hosting»), результат появляется в `v2-deploy`.
Дальше в Plesk: Git → «Получить обновления и развернуть» (при автоматическом режиме это происходит само).

## Если что-то не работает
- Белая страница или 500: права на `storage` и `bootstrap/cache` (должны быть доступны на запись), смотрите `storage/logs/laravel.log`.
- Нет стилей: корень документов указывает не на `public`.
- Нет картинок из админки: не выполнена команда `storage:link`.

## Медос-фарм
Проект для управления предприятием розничной продажи медикаментов:
* SPA VueJS frontend
* VueJS + Twig manager area
* Symfony backend

### editorconfig

Проект включает в себя файл настроек `.editorconfig`. Пожалуйста, установите дополнение на вашу IDE, чтобы использовать его.

### Функционал
* frontend - регистрация, авторизация, восстановление пароля, получение профиля, редактирование профиля
* manager area - авторизация; список всех пользователей с постраничной навигацией; поиск по ФИО, email, телефону; блокировка пользователя; активация пользователя; редактирование полей пользователя;
* отправка писем

### Технологический стек
* php7.4
* Webpack-Encore
* NodeJS + Yarn

## Как развернуть проект
### Скачивание необходимых файлов:
```shell script
git clone https://github.com/dithreil/medos-pharm.git
cd medos-pharm
php composer.phar install
yarn install
```

### Прописать необходимые переменные в env.local:


### Создание базы данных
> создание БД
```shell script
php bin/console doctrine:schema:create
```
> создание таблиц
```shell script
php bin/console doctrine:schema:update --force
```

> создать пользователя-администратора в БД
```shell script
php bin/console app:users:create-admin
```  

### Запуск проекта
> запуск сборки js файлов в режиме слежения
```shell script
yarn run encore dev --watch
```
> запуск встроенного веб-сервера (если установлена команда symfony https://symfony.com/download)
```shell script
symfony server:start
```
> запуск встроенного веб-сервера (если не установлена symfony)
```shell script
php bin/console server:start
```

### Как работать с почтой
Рассылка осуществляется при помощи swiftmailer.
В `.env.local` указать заполнив реальными значениями `example` и `password`:
`MAILER_TRANSPORT=smtp`
`MAILER_USERNAME=example@gmail.com`
`MAILER_PASSWORD=password`
`MAILER_HOST=smtp.gmail.com`
`MAILER_PORT=465`
`MAILER_ENCRYPTION=ssl`
`MAILER_AUTH_MODE=login`

### Проверка кода

> **Используйте команды из Makefile**

#### Бэк

* `make check_back` - запускает проверку `phplint`, `php_codesniffer` и `phpstan`

Отдельные команды:

* `./vendor/bin/phplint`
* `./vendor/bin/phpcs -n`
* `./vendor/bin/phpstan analyse`

Командой `./vendor/bin/phpcbf` возможно исправить ошибки найденные с помощью `phpcs`, но не все

#### Фронт

* `make check_front` - запускает `yarn eslint` и `yarn stylelint`

Отдельные команды:

* `yarn eslint` - проверка javascript
* `yarn eslint_fix` - проверка javascript и автоматическое исправление, где это возможно
* `yarn stylelint` - проверка стилей
* `yarn stylelint_fix` - проверка стилей и автоматическое исправление, где это возможно

### API doc

Для генерации API используется NelmioApiDocBundle

* Для фронта `/api/doc`
* Для админки `/api/doc/admin`

### Авторизация

Url для входа

* Для клиента `/login/client`
* Для работника `/login/employee`
* Для админки `/admin/login`

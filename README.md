# Проект для сбора данных, отслеживания состояний заказов, отслеживания состойний платежей почты России

Краткое описание проекта

## Installation

```bash
git clone git@github.com:enterprise-it-ru/srussian_api_service.git russian_api_service.local
cd russian_api_service.local
composer install
```

Copy the .env file and change the database connection settings

```bash
cp .env.example .env
```

```bash
php artisan key:generate
php artisan storage:link
```

```bash
npm install
```

```bash
npm run prod
```

For development mode, use the command

```bash
npm run watch
```


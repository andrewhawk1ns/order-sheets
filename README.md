# order-sheets

Order sheet generation application.

project name: order-sheets

## Getting Started

### Required Software Package Versions to Install

Node: 12.18.3

MySQL

PHP: 7.2.5+

### Install Dependencies

Client

```
yarn
```

Server

```
composer install
```

### Environment Setup

Copy .env.example to .env

### Install the Application Key

Generate Laravel's application key

```
php artisan key:generate
```

### Create the symbolic link

Link the storage directory with the public directory

```
php artisan storage:link
```

### Run the Project

Run the dev server

```
php artisan serve
```

Compile the client

```
yarn dev
```

### Testing

Client

```
yarn test
```

Server

```
php artisan test
```

### Linting

```
yarn lint
```

# Task and Notes
In this Laravel application, users can utilize APIs to create tasks accompanied by multiple notes, each of which can contain multiple attachments.

# Laravel 10 API project

This is a API application made on Laravel

## Installation

Follow these steps to install the project locally:

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL or any other compatible database

### Clone the repository

```bash
git clone https://github.com/der-pun/task-and-notes.git
```

### Install Php Dependencies
```
composer install
```

### Environment Configuration

- Duplicate the .env.example file and rename it to .env.
- Update the .env file with your local environment settings:
- Set DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD for database configuration.
- Generate an application key by running:
```
php artisan key:generate
```

### Database Setup
- Create a new database in your MySQL server.
- Run the database migrations to create tables and seed the database with sample data:
``` 
  php artisan migrate 
  php artisan db:seed 
```

### Install Passport
```
  php artisan passport:install
```

## Test Credentials
Email = johndoe@example.com
Password = securePassword123

### Bearer Token
We use Laravel Passport to generate tokens to authenticate API requests. 
- Login API is used to generate the token
- The generated token can be used as "bearer token" for subsequent API requests
- Each token expires after 1 day

## View API Documentation
Postman Link - https://documenter.getpostman.com/view/32892332/2sA2xfZDqi
# Symfony Pricing Engine

![Tests](https://github.com/elgaspar/symfony-pricing-engine/actions/workflows/tests.yml/badge.svg)
[![codecov](https://codecov.io/github/elgaspar/symfony-pricing-engine/graph/badge.svg?token=TUNZU56A2F)](https://codecov.io/github/elgaspar/symfony-pricing-engine)
![PHPStan](https://github.com/elgaspar/symfony-pricing-engine/actions/workflows/static-analysis.yml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-8.4+-blue)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

A demonstration project showcasing a flexible and extensible pricing engine built with Symfony.  
It provides a REST API to manage products and a `/checkout` endpoint that calculates the final  
cart price by applying different discount strategies.

Each product has a base price and can have one of the following discount strategies applied:

- Fixed Discount
- Percentage Discount
- Buy One Get One Free

This project highlights clean code, modular design, and clear API structure, making it easy to  
extend with additional business rules or discount strategies.

## Requirements

To run this project, you need:

- PHP 8.4 or higher
- Composer
- Symfony CLI
- Docker & Docker Compose

## Installation & Setup

1. **Copy the environment file**:
    ```bash
    cp .env.example .env.local
    ```
   Then add your API token inside. You can generate a token with:
    ```php
    php -r "echo bin2hex(random_bytes(32));"
    ```
2. **Install PHP dependencies**:
    ```bash
    composer install
    ```
3. **Start the database with Docker**:
    ```bash
    docker-compose up -d
    ```
4. **Run database migrations**:
   For the main database:
    ```bash
   php bin/console doctrine:migrations:migrate
    ```
   For the test database:
    ```bash
    php bin/console doctrine:database:create --env=test
    php bin/console doctrine:migrations:migrate --env=test
    ```
5. **Start the Symfony development server**:
    ```bash
   symfony server:start
    ```
   The API will be accessible at:
    ```
   http://localhost:8000/api/v1
   ```

## API

### Base URL

```
http://localhost:8000/api/v1
```

### Available endpoints

| Method | Endpoint       | Description                                   |
|--------|----------------|-----------------------------------------------|
| GET    | /products      | List all products                             |
| GET    | /products/{id} | Retrieve a single product                     |
| POST   | /products      | Create a new product                          |
| PUT    | /products/{id} | Update an existing product                    |
| DELETE | /products/{id} | Delete a product                              |
| POST   | /checkout      | Calculate the total cart price with discounts |

### Authentication

All API requests require a token in the `Authorization` header:

```
Authorization: Bearer your_api_token
```

The token should match the one set in your `.env.local`.

### OpenAPI Documentation

The API is documented using [OpenAPI 3.2.0](https://www.openapis.org/).
Full details are available in the `openapi.yaml` file.

### Postman Collection

Postman collection and environment files are saved in the `postman/` directory.  
You can import them in Postman to quickly start testing the API.

## Tests

The project uses [PHPUnit](https://phpunit.de/) for unit and integration tests, with a separate test
database to avoid interfering with the development database.

Run the tests using Composer:

```bash
composer test
```

Tests automatically use the `.env.test` file and a dedicated database named `app_test`.

## Code Quality Tools

The project uses the following tools to ensure clean, maintainable code, catch errors early,
and keep the codebase consistent:

- [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/) for PSR-12 coding standards:

```bash
composer phpcs
composer phpcbf
```

- [PHPStan](https://phpstan.org/) for static analysis:

```bash
composer phpstan
```

## Continuous Integration

This project uses GitHub Actions to automatically run tests, static analysis, and generate code coverage
on every push or pull request.

- The `.github/workflows/tests.yml` workflow runs PHPUnit with Xdebug to collect code coverage, which is uploaded to
  [Codecov](https://codecov.io/) for visualization and badge updates.
- The `.github/workflows/static-analysis.yml` workflow runs PHPStan to check for type errors and potential issues.

## Architecture

### Separation of Persistence Layer

The project separates persistence from business logic. Doctrine and Symfony entities are used
solely for database interactions, while the application uses its own domain models and value
objects for core business logic. This approach ensures a clean architecture, easier testing,
and better maintainability.

### Caching

Doctrine is enabled in the production environment to improve performance. A production-ready
system could also leverage HTTP-level caching (e.g., Cache-Control headers) for additional
performance gains, though it was not implemented in this project.

## Author

Elias Gasparis

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

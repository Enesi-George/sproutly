# Sproutly - Transaction Ledger API

A microservice Laravel-based transaction ledger api with real-time event streaming.

## Features
-   Microservice architecture
-   Wallet management with debit/credit transactions
-   Real-time event streaming with Kafka
-   Excel Exportation for transacton history
-   RESTful API with documentation
-   Dockerized development environment
-   PEST test 

## Prerequisites

-   Docker
-   Docker Compose

## Quick Start

1. Clone the repository:

```bash
git clone <your-repo-url>
cd sproutly
```

2. Environment variable

```bash
cp .env.example .env
```

3. Start docker service

```bash
docker compose up -d --build
```

4. Setup the application on docker container

```bash
docker exec -it sproutly-app bash
composer install
php artisan key:generate
php artisan migrate
```

## API documentation

-   http://127.0.0.1:8000/docs/api

## Test Coverage

```bash
docker exec -it sproutly-app bash
php artisan test
```

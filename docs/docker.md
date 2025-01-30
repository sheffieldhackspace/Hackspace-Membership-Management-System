# Docker

To help with local dev we have a version of [laravel sail](https://laravel.com/docs/11.x/sail) that is pre-configured for the project. This is a great way to get started with the project without having to worry about setting up your local environment.

## Containers
The project uses the following containers:
- laravel: The main PHP container - [localhost:80](http://localhost:80)
- mysql: The mysql database container
  - external localhost:3306
  - internal laravel:3306
- redis: The redis container 
  - external localhost:6379
  - internal redis:6379
- mailpit: Catches all outbound email 
  - HTTP [localhost:8025](http://localhost:8025)
  - SMTP 
    - external localhost:1025
    - internal mailpit:1025

## Getting Started
Once you have docker installed on your machine you can run the following command to start the project:

To run artisan or composer commands you can run either of the following commands:
```bash
./vendor/bin/sail <command>
docker-compose laravel exec <command>
```
The vendor sail command doesn't work for some setups so you may need to use the docker-compose command.

If you are running the project for the first time you will need to run the following command to install the dependencies:
```bash
cp .env.example .env
docker-compose laravel exec --rm composer install
docker-compose laravel exec npm ci
docker-compose laravel exec php artisan migrate --seed
docker-compose laravel exec php artisan key:generate
docker-compose laravel exec php artisan typescript:transform
```

Once you have run the above commands you can run the following command to start the project:
```bash
docker-compose up 
```

In another tab you may also want to run to watch for front end changes: 
```bash
docker-compose exec laravel npm run
```

You can now navigate to [localhost:80](http://localhost:80) to view the project.

To run tests you can run the following command:
```bash
docker-compose exec laravel php artisan test --env=testing
```

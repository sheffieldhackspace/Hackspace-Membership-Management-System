# Hackspace Membership Management System

This system is designed to allow easy management of members of Sheffield Hackspace. It is designed to be used by the
members themselves, as well as the administrators of the Hackspace.

The system will allow:

- [ ] Members to sign up to the Hackspace
- [ ] Members to pay their membership fees
- [ ] Members to view their membership status
- [ ] Members to view their training status
- [ ] Members to sign up for and/or request training
- [x] Administrators to view the membership status of all members
- [ ] Administrators to add and remove members
- [ ] Administrators to view the payment status of all members
- [ ] Administrators to balance bank statements with the membership database
- [ ] Portland Works to view who has access to the building
- [ ] Trainers to view who has signed up for their training sessions
- [ ] Trainers to view who requested training
- [ ] Trainers to mark members as having attended and/or completed training

## Documentation

* [Database](docs/database.md)
* [Permission Model](docs/permission-model.md)
* [Docker](docs/docker.md)

## Development

The backend is built in [Laravel 11](https://laravel.com/docs/11.x) and uses a MySQL database we use the following
packages:

* [Laravel Passport](https://laravel.com/docs/11.x/passport) and socialite for authentication.
* [Spatie Permissions](https://spatie.be/docs/laravel-permission/v6/introduction) for role based access control.
* [Spatie typescript-transformer](https://spatie.be/docs/typescript-transformer/v2/introduction) for typescript support.
* [Discord PHP](https://github.com/discord-php/DiscordPHP) for discord integration.
* [Inertia](https://inertiajs.com/) for routing.

The frontend is built in Typescript with [Vue.js 3](https://vuejs.org/) for a framework
and [Tailwind 3](https://v3.tailwindcss.com/docs/installation) for styling.

### Installation

1. Clone the repository
2. Run

``` shell
composer install
npm ci
php artisan migrate --seed
php artisan key:generate
php artisan typescript:transform
php artisan ziggy:generate

```

### Running the application

4. Run `npm run dev`
9. Run `php artisan serve`

### Testing

1. Run `php artisan test`

### Making backend end changes

#### Adding Models

If you make changes to models or add new DTOs you will need to run `php artisan typescript:transform` to update the
typescript interfaces.

You should also run `php artisan ide-helper:models -RW` to update the phpdoc blocks in the models.

#### Adding Routes

If you add a new route you will need to run to update the route cache and generate the ziggy routes file.

``` shell
php artisan route:cache
php artisan ziggy:generate
```

#### Linting

We use PHPStan and Laravel Pint for linting and static analysis. You can run the following to run these

``` shell
composer lint:fix
composer stan
```

#### Testing

We use phpunit for testing. You can run them via `composer run test`

### Making front end changes

After making front end changes you can run `npm run lint:fix` to fix any linting errors.

## Contributing

A high percentage of quality test coverage is expected for all new code.
a pull request.
Front end and back and tests are expected for all new features.
Please do not be offended if your pull request is rejected for not meeting these standards.
This is to make it easier for all developers to work on the project in the future.

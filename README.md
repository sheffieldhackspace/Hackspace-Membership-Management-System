# Hackspace Membership Management System

This system is designed to allow easy management of members of Sheffield Hackspace. It is designed to be used by the members themselves, as well as the administrators of the Hackspace.

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
 * [Database](./documentation/database.md)
 * [Permission Model](./documentation/permission-model.md)

## Development
The backend is built in Laravel 11 and uses a MySQL database we use the following packages:
* Laravel Pasport and socialite for authentication.
* Spatie Permissions for role based access control.
* Spatie typescript-transformer for typescript support.

The frontend is built in Vue.js 3 and uses inertia for routing. New front end components should be built in typescript not using php and blade.

### Installation
1. Clone the repository
2. Run 
``` shell
composer install
npm ci
php artisan migrate --seed
php artisan key:generate
php artisan typescript:transform
```

### Running the application
4. Run `npm run dev`
9. Run `php artisan serve`

### Testing
1. Run `php artisan test`

### Making backend end changes
If you make changes to models or add new DTOs you will need to run `php artian typescript:transform` to update the typescript interfaces.
You should also run `php artisan ide-helper:models -RW` to update the phpdoc blocks in the models.

### Making front end changes
If you make changes to models or add new DTOs you will need to run `php artisan typescript:transform` to update the typescript interfaces.

## Contributing
A high percentage of quality test coverage is expected for all new code. Please run `php artisan test` before submitting a pull request.
Front end and back and tests are expected for all new features.
Please do not be offended if your pull request is rejected for not meeting these standards.
This is to make it easier for all developers to work on the project in the future.

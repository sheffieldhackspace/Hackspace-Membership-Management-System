# Permission Model

Owning to the requirements the system has a slightly complex permission model:
* A user can be assigned multiple members to be responsible for. IE a parent can be responsible for their children.
* Portland Works need to have access to a section of the system to view who has access to the building. The Portland Works users they do not have a member assigned to them.
* Roles for members are driven by there membership status. IE a member can be a member, a member and a trainer etc.

This has meant we have two different models that can have roles assigned to them. The first is the user model which is used for authentication and the second is the member model which is used to represent a member of the hackspace.
The vast majority of Users will never have any roles assigned to them. Their roles will be driven by their membership status and other faccets of the member model.

## Roles
Roles should only be used to determine what a user/member can do within a section of the system. This should be done with permissions assigned to that role. Roles should only exist to be a container for permissions.

### User Roles
* `Portland Works` - This role is assigned to Portland Works users. They have access to a section of the system to view who has access to the building. The Portland Works users they do not have a member assigned to them.

### Member Roles
* `Member` - This role is assigned to all paying members of the hackspace and removed from all none paying members. It is the base role for all members.
* `Keyholder` - This role is assigned to members who are paying keyholder members and removed from none paying keyholder members.
* `Trainer` - This role is assigned to members who are trainers. They can view who has signed up for their training sessions, view who requested training, and mark members as having attended and/or completed training.
* `Admin` - This role is assigned to members who are administrators of the system Trusees automatically have this assigned. They can view the membership status of all members, add and remove members, view the payment status of all members, balance bank statements with the membership database, and view who has access to the building.

## Development
The system uses the [Spatie Permissions](https://spatie.be/docs/laravel-permission/v5/introduction) package to manage permissions and laravel policys to manage authorization. This package is well documented and should be used as a reference for any development work.

For backend authorization we aim to use middlewear as defined in the route files as much as possible. That way any request without permission never makes it to a controller. This can be backed up with checks within the controllers if needed.

On the front end we can use the `HasPermission` component to show or hide elements based on the users permissions. This should be used when possible though it can lead to a poor user experience if not used correctly.

### Adding new permissions
When adding new permissions you should add them to the `Permission` enum in the `app/Enums` folder. This will allow you to use the enum in the code and ensure that the permission is always spelled correctly. You should also add the permission to the `RolesAndPermissionsSeeder` so that it is added to the database.

### Adding new roles
We should not need to add new roles regularly if you think it is really needed please discuss with the team first. If you do need to add a new role you should add it to the `Roles` enum in the `app/Enums` folder. This will allow you to use the enum in the code and ensure that the role is always spelled correctly. You should also add the role to the `RolesAndPermissionsSeeder` so that it is added to the database.

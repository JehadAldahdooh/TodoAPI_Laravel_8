
## Todo API using laravel 8

Todo list API using JWT authentication using the use the Repository Pattern. The database used in this project is PostgreSQL.


## How to run the API

- Create a database named todo
- Run composer install
- Run php artisan migrate
- Run php artisan serve --port= xxxx
- using Postman to check the api requests

## API ROUTES

- POST /api/v1/signup: Sign up as an user of the system, using email & password
- POST /api/v1/signin: Sign in using email & password. The system will return the
JWT token that can be used to call the APIs that follow
- PUT /api/v1/changePassword: Change user’s password
- GET /api/v1/todos?status=[status]: Get a list of todo items. Optionally, a status
query param can be included to return only items of specific status. If not
present, return all items
- POST /api/v1/todos: Create a new todo item
- PUT /api/v1/todos/:id: Update a todo item
- DELETE /api/v1/todos/:id: Delete a todo item
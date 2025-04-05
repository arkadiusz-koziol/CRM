CRM

In this project we have two main folders:
- .docker
- backend
- frontend

In the backend folder we have the API of the project written in PHP with Laravel 11.
Frontend has been written in react.

How to run project? 
Just copy .env.example to .env in backend and frontend directories, then run the command `docker-compose up -d` in the .docker directory.

How to run seeders? 
- Run the command `./.docker/php/run_seeder.sh` in the root of the project.
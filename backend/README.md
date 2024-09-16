## How to run project's backend:
1. Clone the repository
2. Copy the .env.example file to .env
3. Go to the directory ```.docker``` and run the command ```docker-compose up -d```
4. You should have:
    - A container running with the PHP
    - A container running with the Postgres database
    - A container running with the Nginx as reverse proxy
    - A container running with the Redis as cache
5. If eveything is ok, you can access the project at http://localhost:8199

## Swagger
The project has a swagger documentation that can be accessed at http://localhost:8199/api/documentation

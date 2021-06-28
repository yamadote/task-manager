# task-manager
:zap: Just task managing and time traking app.

## Setup
Initialize project dependencies:
```
composer install
npm install --force
```

Copy env files:
```
cp ./docker/.env.dist ./docker/.env
cp ./.env ./.env.local
```

Start dev environment:
```
npm run watch 
```
```
cd docker
docker-compose up
```

Setup database:
```
docker exec -it docker_php_1 /bin/bash
bin/console doctrine:migrations:migrate
bin/console app:create-user test@email.com password
```


initialize project dependencies
```
composer install
npm install --force
```

create env files
```
cp ./docker/.env.dist ./docker/.env
cp ./.env ./.env.local
```

start dev environment
```
npm run watch  
cd docker
docker-compose up
```

setup database
```
docker exec -it docker_php_1 /bin/bash
bin/console doctrine:migrations:migrate
bin/console app:create-user test@email.com password
```

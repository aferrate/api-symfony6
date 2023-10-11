```
cd laradock
docker-compose up -d nginx mysql phpmyadmin redis mailhog elasticsearch rabbitmq redis-webui
docker-compose exec workspace bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console app:create-elasticsearch-cars-index
php bin/console app:create-elasticsearch-users-index
php bin/console app:delete-elasticsearch-cars-index
php bin/console app:delete-elasticsearch-users-index
php bin/console messenger:consume async -vv
net stop winnat
net start winnat
wsl -d docker-desktop
sysctl -w vm.max_map_count=262144
phpunit
```
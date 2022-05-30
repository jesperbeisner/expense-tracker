docker-compose exec php bin/console doctrine:database:drop --force
docker-compose exec php bin/console doctrine:database:create -n
docker-compose exec php bin/console doctrine:migrations:migrate -n
docker-compose exec php bin/console make:migration -n
docker-compose exec php bin/console doctrine:migrations:migrate -n
docker-compose exec php bin/console app:create-admin-user test@test.de test
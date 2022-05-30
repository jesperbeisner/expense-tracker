# expense-tracker

Work in progress. Small application to track my monthly expenses.

## Setup

Create the database
```bash
docker-compose exec php bin/console doctrine:database:create -n
```

Load the migrations
```bash
docker-compose exec php bin/console doctrine:migrations:migrate -n
```
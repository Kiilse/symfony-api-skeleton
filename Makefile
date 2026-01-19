.PHONY: help install up down build restart shell composer migrate db-reset

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installe les dépendances et configure le projet
	docker-compose exec php composer install
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

up: ## Démarre les conteneurs Docker
	docker-compose up -d

down: ## Arrête les conteneurs Docker
	docker-compose down

build: ## Construit les images Docker
	docker-compose build

restart: ## Redémarre les conteneurs
	docker-compose restart

shell: ## Ouvre un shell dans le conteneur PHP
	docker-compose exec php sh

composer: ## Exécute composer (usage: make composer CMD="require package")
	docker-compose exec php composer $(CMD)

migrate: ## Exécute les migrations
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Réinitialise la base de données (ATTENTION: supprime toutes les données!)
	docker-compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec php php bin/console doctrine:database:create
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

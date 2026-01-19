# Symfony API Skeleton

Skeleton minimal pour démarrer rapidement une API REST Symfony avec PostgreSQL et Docker, suivant une architecture Domain-Driven Design (DDD) avec Doctrine DBAL.

## Architecture

Ce skeleton suit une architecture en couches inspirée de l'Hexagonal Architecture / Clean Architecture :

- **Domain** : Contient les modèles métier, Value Objects, interfaces de repositories et exceptions du domaine
- **Application** : Contient les Command/Query handlers (CQRS), DTOs et la logique applicative
- **Infrastructure** : Contient les implémentations concrètes (repositories Doctrine DBAL, contrôleurs HTTP, etc.)

## Stack

- **Symfony**: 8.x (skeleton minimal)
- **Base de données**: PostgreSQL 16 avec Doctrine DBAL (pas ORM)
- **Docker**: `docker-compose` avec PHP-FPM, Nginx et PostgreSQL
- **Architecture**: DDD avec CQRS (Command/Query Responsibility Segregation)

## Structure du projet

```
src/
├── Domain/                    # Couche domaine
│   ├── Product/              # Exemple d'entité métier
│   │   ├── Model/           # Modèle Product
│   │   ├── ValueObject/     # ProductId
│   │   ├── Repository/      # ProductRepositoryInterface
│   │   └── Exception/       # Exceptions du domaine
│   └── Shared/              # Éléments partagés
│       ├── Exception/       # DomainException, InvalidUuidException
│       └── ValueObject/    # Uuid
├── Application/              # Couche application
│   └── Product/
│       ├── Command/         # CreateProductCommand + Handler
│       ├── Query/           # GetProductQuery + Handler
│       └── DTO/             # ProductResponseDTO
└── Infrastructure/           # Couche infrastructure
    ├── Product/
    │   ├── Persistence/     # DoctrineProductRepository (DBAL)
    │   └── Http/            # ProductController
    └── Shared/
        └── Persistence/      # UuidType pour Doctrine
```

## Prérequis

- Docker + Docker Compose
- Make (optionnel, pour utiliser les commandes du Makefile)

## Démarrage rapide

1. **Cloner/copier ce skeleton** dans un nouveau dossier

2. **Démarrer les conteneurs Docker** :
   ```bash
   make up
   # ou
   docker-compose up -d
   ```

3. **Installer les dépendances et configurer la base de données** :
   ```bash
   make install
   # ou
   docker-compose exec php composer install
   docker-compose exec php php bin/console doctrine:database:create --if-not-exists
   docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Accéder à l'API** :
   - API disponible sur `http://localhost:8080`
   - Healthcheck : `GET http://localhost:8080/api/health`
   - Products : `GET http://localhost:8080/api/v1/products/{id}`

## Endpoints disponibles

### Healthcheck
- `GET /api/health` - Vérifie que l'API fonctionne

### Products (exemple complet)
- `POST /api/v1/products` - Créer un produit
  ```json
  {
    "name": "Product Name",
    "description": "Product description",
    "price": 29.99
  }
  ```
- `GET /api/v1/products/{id}` - Récupérer un produit par ID

## Configuration base de données

Dans `docker-compose.yaml` :
- Service `postgres` (PostgreSQL 16)
- Variables par défaut :
  - `POSTGRES_DB=symfony_api`
  - `POSTGRES_USER=symfony`
  - `POSTGRES_PASSWORD=symfony`

La variable d'environnement `DATABASE_URL` est injectée dans le service `php` :
```
postgresql://symfony:symfony@postgres:5432/symfony_api?serverVersion=16
```

## Commandes Make disponibles

- `make help` - Affiche l'aide
- `make install` - Installe les dépendances et configure le projet
- `make up` - Démarre les conteneurs Docker
- `make down` - Arrête les conteneurs Docker
- `make build` - Construit les images Docker
- `make restart` - Redémarre les conteneurs
- `make shell` - Ouvre un shell dans le conteneur PHP
- `make composer CMD="require package"` - Exécute une commande composer
- `make migrate` - Exécute les migrations
- `make db-reset` - Réinitialise la base de données (ATTENTION: supprime toutes les données!)

## Utiliser ce projet comme skeleton

1. **Copier le dossier** vers un nouveau projet

2. **Renommer dans `composer.json`** :
   - Mettre à jour `name` et `description`

3. **Configurer la base de données** :
   - Ajuster `docker-compose.yaml` si nécessaire (nom de base, user, password)

4. **Créer tes propres entités** :
   - Suivre le pattern `Domain/Product` pour créer tes propres entités métier
   - Créer les Value Objects, Repository Interfaces dans `Domain`
   - Créer les Command/Query handlers dans `Application`
   - Implémenter les repositories DBAL dans `Infrastructure/Persistence`
   - Créer les contrôleurs HTTP dans `Infrastructure/Http`

5. **Créer les migrations** :
   ```bash
   make shell
   php bin/console doctrine:migrations:generate
   # Éditer le fichier de migration généré dans migrations/
   ```

## Exemple complet : Product

Ce skeleton inclut un exemple complet avec l'entité `Product` :

- **Domain** : `Product` (modèle), `ProductId` (Value Object), `ProductRepositoryInterface`
- **Application** : `CreateProductCommand`/`Handler`, `GetProductQuery`/`Handler`, `ProductResponseDTO`
- **Infrastructure** : `DoctrineProductRepository` (DBAL), `ProductController`
- **Migration** : `Version20260119142049.php` crée la table `products`

Tu peux utiliser cet exemple comme référence pour créer tes propres entités.

## Doctrine DBAL vs ORM

Ce skeleton utilise **Doctrine DBAL** (pas ORM) pour :
- Plus de contrôle sur les requêtes SQL
- Meilleure performance pour des APIs simples
- Architecture plus explicite (pas de mapping automatique)

Les repositories utilisent directement `Connection` de DBAL avec `createQueryBuilder()` et `insert()`/`update()`/`delete()`.

## Notes importantes

- Ce skeleton est volontairement minimal : pas d'authentification par défaut
- Tu peux ajouter LexikJWTAuthenticationBundle si besoin (voir `Dont-Be-Lazy-API` pour référence)
- La structure Domain/Application/Infrastructure est prête pour être étendue
- Les migrations sont dans le dossier `migrations/`

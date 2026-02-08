# BUG / ISSUE TRACKER

## Stack utilisée

- Symfony 8.0
- PHP 8.4
- Docker
- NGINX
- MYSQL 8.0

## Architectures utilisées

- MVC
- CQRS
- Event Driven Design

## Installation du projet

### Pré-requis

- Docker & Docker Compose

### Étapes d’installation

Pour installer le projet, exécutez les commandes suivantes dans l’ordre :

1. `docker compose up -d`
2. `docker compose exec fpm composer install --no-cache`
3. `docker compose exec fpm php bin/console doctrine:migrations:migrate`
4. `docker compose exec fpm php bin/console doctrine:fixtures:load`

### Lancement du projet

Une fois les commandes précédentes exécutées correctement, vous pouvez accéder au projet à l’adresse
suivante : [http://localhost/](http://localhost)

Pour vous connecter, 3 utilisateurs sont disponibles:

```
  - administrator : admin@issue-tracker.com / 123123
  - user 1 : user01@issue-tracker.com / 123123
  - user 2 : user02@issue-tracker.com / 123123
```

### Quelques liens utiles:

- [http://localhost:1080](http://localhost:1080) - Mailcatcher : pour voir les emails envoyés
- [http://localhost:9002](http://localhost:9002) - PhpMyADMIN

## Lancement des tests unitaires

### Configuration de la base de données de tests:

Fichier de configuration : `.env.test`

Chaîne de connexion :

```
DATABASE_URL="mysql://root:secret@database/app?serverVersion=8.0.32&charset=utf8mb4"
```

### Préparation avant les tests unitaires:

Avant d’exécuter les tests, lance les commandes suivantes (si elles n’ont pas déjà été exécutées) :

```
php bin/console doctrine:database:drop -f --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:fixtures:load --env=test   # pour charger les utilisateurs
```

### Exécution des tests

Générer le rapport de couverture :

```
./vendor/bin/phpunit --coverage-html var/tests/coverage
```

Mode debug :

```
./vendor/bin/phpunit tests --display-deprecations --display-notices --display-warnings --debug
```

Visualisation des statistiques:

Pour consulter la page des statistiques (sous Linux) :

```
python3 -m http.server 8000
```

Les tests écrits ne couvrent pas l’ensemble du code. Cependant, j’ai essayé de rédiger différents tests unitaires: base
de données de test, mocks, formulaires ...

Voici un exemple du niveau de couverture des tests.

![img.png](doc/img.png)

Note : le code non couvert correspond à la partie "Member". Je ne voulais pas me répéter.

## Architecture du projet:

Le projet se base sur l'architecture MVC (de Symfony) en combinaison avec CQRS (Command Query Responsibility
Segregation) et Event Driven Design

- Event Driven Design

![Event_Driven_Design.png](doc/Event_Driven_Design.png)

- CQRS : Partie Commande

![Command_Design.png](doc/Command_Design.png)

Le service `IssueCommandHandler` recoit une instance de `IssueCommandInterface`. Il ne sait pas quelle instance va
recevoir. C'est une sortie de `Strategy Design Pattern` car nous fournissons la strategy (la commande) en runetime

- CQRS : Partie Query

![Query_Design.png](doc/Query_Design.png)

J’ai conçu l’interface `IssuesPaginatorRequesterInterface` car nous ne pouvons pas garantir que le service utilisé
communiquera toujours avec une base de données. Il est possible que nous souhaitions étendre cette fonctionnalité afin
de dialoguer avec une API. Il suffit alors d’implémenter `IssuesPaginatorRequesterInterface` et de développer la logique
correspondante.

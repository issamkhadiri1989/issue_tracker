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

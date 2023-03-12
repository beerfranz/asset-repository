# AssetRepository

The goal of this project is to build an inventory database of assets with this specificities:
* OpenSource
* Cloud service
* ISO 27001 compliant

## Architecture

Caddy (web server) => PHP => Database (ex: Postgres)

## Definitions

* Asset: it can be what you want: a server, a container, a Kubernetes pod, etc...
  * owner: the owner of the asset (ISO 27001 requirement)
  * type (defined bellow)
  * attributes: a free field
  * audits (defined bellow)
* Asset type: define a category (server, container, pod, etc...)
* Asset audits: track modifications on assets (create, update, remove)

## Security

Authentication is managed by Caddy. You can configure basic auth or OAuth integration.
Caddy add HTTP headers containing user identifier and roles.
The PHP app use this HTTP headers to check authorizations.

## Developer guide

### Tests

#### TL;DR

Init the test database and run tests with phpunit:

```
docker compose exec php php bin/console --env=test doctrine:database:create
docker compose exec php php bin/console --env=test doctrine:schema:create

docker compose exec php bin/phpunit --coverage-html tests/code_coverage/
```

**Definitions**
* Factory: Insert entities in the database. Use `zenstruck/foundry` and `fakerphp/faker`

#### Init

Create the test database (isolated database)
```
docker compose exec php php bin/console --env=test doctrine:database:create
docker compose exec php php bin/console --env=test doctrine:schema:create
```

#### Run tests

Run tests with PHPUnit

```
docker compose exec php bin/phpunit --coverage-html tests/code_coverage/
```

#### Create test data

Foundry factories are used to generate random data.
Factories are stored in `tests/Factory` folder
```
docker compose exec php php bin/console make:factory --test
```

## Credits

### ApiPlatform

The official project documentation is available **[on the API Platform website](https://api-platform.com)**.
Created by [Kévin Dunglas](https://dunglas.fr). Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).

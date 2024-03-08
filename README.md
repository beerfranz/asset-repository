# AssetRepository

**This project is in active development, still not validated in production environment**

The goal of this project is to build an inventory database of assets with this specificities:
* OpenSource
* Cloud service
* ISO 27001 compliant

## Architecture

Caddy (web server) => PHP => Database (ex: Postgres)


## Definitions

* Asset: it can be what you want: a server, a container, a Kubernetes pod, etc...  eq: Catalog
  * owner: the owner of the asset (ISO 27001 requirement)
  * type (defined bellow)
  * attributes: a free field
  * audits (defined bellow)
* Asset type: define a category (server, container, pod, etc...)  => Template ?
* Asset audits: track modifications on assets (create, update, remove)

* Instance: something that is or was running. eq: Inventory
  * optionaly (prefered) link to an asset
  * STATE:
    * Servers: TODO
    * Pod: TODO
    * Container: TODO
    * systemd: TODO

TO DO:
* Change review plan with proof (provided by automatisation ?)
  * owner needed (can by someone or a team/group)
    * maybe different kind of owners, at least a main owner, + maybe a legal owner
  * version state (dates, old)
    * NPM: compliqué en curl, des outils npm existent: npm view express time --json => un exporter dans la CI ?
* Data view: kind of data, assets using this data, owner, confidentiality
* QA: test plan
* revue des taches où je suis owner
  * prévoir des dates (temps nécessaire, qui ?)
* export des assets aws, azure
* notion de parent en plus des relations ? ou split relations en parent, data, group ?
  * ou différentes vues dans asset. ex: backend (parent | vue macro) => backend-helm + ecs-task + container (vue micro)


module Versions:
* registry: collection of packages
  * package: attributes
  * version: just a number
* release: collection of packages

POC:
- envDef:
  - develop
  - preprod
  - prod:
      - fr1
      - us1
- versionWorkflow: workflow1:
  - step0: develop
  - step1: preprod
  - step2: prod
- asset angular
  - workflow1

data types:
* tags: array
* labels: key-value
* attributes: object


Product (ex: backend, front)
* have assets
* sub-products

## UserStories

1. new version
  1. declare new version
  2. follow workflow

## Security

Authentication is managed by Caddy. You can configure basic auth or OAuth integration.
Caddy add HTTP headers containing user identifier and roles.
The PHP app use this HTTP headers to check authorizations.

## Developer guide

### Performances

**Memory**
* https://stackoverflow.com/questions/26616861/memory-leak-when-executing-doctrine-query-in-loop
* https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/batch-processing.html#iterating-large-results-for-data-processing
* https://accesto.com/blog/how-to-improve-code-performance-in-5-easy-steps/
* Debug memory usage:
```
echo '<pre>';
$vars = get_defined_vars();
foreach($vars as $name=>$var)
{
    echo '<strong>' . $name . '</strong>: ' . strlen(serialize($var)) . '<br />';
}
exit();
```

### Tests

#### TL;DR

Init the test database and run tests with phpunit:

```
docker compose exec php php bin/console --env=test doctrine:database:create
docker compose exec php php bin/console --env=test doctrine:schema:create

docker compose exec php bin/phpunit --coverage-html tests/test_coverage/
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
docker compose exec php bin/phpunit --coverage-html tests/test_coverage/
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

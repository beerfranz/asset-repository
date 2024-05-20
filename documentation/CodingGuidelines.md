# Coding guidelines

## Modules

To add a new modules.

(https://medium.com/@mounabenhmida/symfony-4-how-to-customize-non-standard-directories-ee10b57abf91)

* Update routes `api/config/routes.yaml`:

```
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

tasks:
    resource:
        path: ../src/Tasks/Controller
        namespace: App\Tasks\Controller
    type: attribute
    # prefix: /ui/tasks
```

* Update API platform `api/config/packages/api_platform.yaml`

```
api_platform:
    mapping:
        paths:
        - '%kernel.project_dir%/src/Entity'
        - '%kernel.project_dir%/src/ApiResource'
        - '%kernel.project_dir%/src/Tasks/ApiResource'
```

* Update doctrine `api/config/packages/doctrine.yaml`

```
doctrine:
    orm:
        mappings:
            App:
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
            Tasks:
                is_bundle: false
                type: attribute
                dir: '%kernel.project_dir%/src/Tasks/Entity'
                prefix: 'App\Tasks\Entity'
                alias: Tasks
```

## Entity

### Json_document

```
#[ORM\Column(type: 'json_document')]
private array $workflow = [];
```

When using a `json_document`, always add mappers to `api/config/packages/doctrine_json_odm.yaml`

```
dunglas_doctrine_json_odm:
  type_map:
    frequency: App\Entity\Frequency # OK
    trigger: App\Entity\Trigger     # OK
    App\Entity\TaskWorkflowStatus: App\Tasks\Entity\TaskWorkflowStatus # This is because mappers wasn't used before
```

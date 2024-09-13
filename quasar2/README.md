# asset-repo (asset-repo)

asset-repo description

## Install the dependencies
```bash
yarn
# or
npm install
```

### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```


### Build the app for production
```bash
quasar build
```

### Customize the configuration
See [Configuring quasar.config.js](https://v2.quasar.dev/quasar-cli-vite/quasar-config-js).


### Generate resource from Api Plateform:
```bash
npm init @api-platform/client http://caddy:8080/docs.jsonopenapi src/ -- --generator quasar --format openapi3 --resource indicator
```

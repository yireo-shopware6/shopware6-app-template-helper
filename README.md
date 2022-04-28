# YireoAppTemplate plugin for Shopware 6
This plugin includes developer tools for helping you to develop App Templates (aka Twig Templates as part of the App System). Note that this does not include App Scripts.

## Installation
Be a Shopware dev.

## Usage
Normally, Twig templates are scanned during app installation and then copied into the database table `app_template`. Run the following command to re-scan your app (in this case, `SwagExample`) for a new `Resources/views/storefront/`-based Twig template - after it has been installed and activated:
```bash
bin/console yireo:app-template:refresh SwagExample
```

Or leave out the app name to refresh all templates of all apps:
```bash
bin/console yireo:app-template:refresh SwagExample
```

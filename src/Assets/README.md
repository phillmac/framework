# UserFrosting/Assets

**Assets** is a library originally created for UserFrosting 4 to make it much easier to reference frontend assets in both production and development contexts.

Out of the box it can:

- Provide an easy way to generate an absolute url to an asset via a locator.
- Provide a basic level of integration with [`gulp-bundle-assets`](https://github.com/dowjones/gulp-bundle-assets) and complete integration with [`@userfrosting/gulp-bundle-assets`](https://www.npmjs.com/package/@userfrosting/gulp-bundle-assets), making it easy to reference asset bundles.
- Integrate via a common interface with virtually any bundling system.
- Integrate with Slim to serve assets that are inaccessible from the public folder, in the development context.
- Perform url-to-path transformations. Useful for making debuggable URLs that can be reverted back to the path to be used by the Slim asset server.
- Integrate with your preferred (and extendable) templating engine to provide easy access to asset bundles (that get wrapped with the appropriate tags) and individual assets.

## Usage

To use Assets, you will need:

- An instance of `ResourceLocator`, where you can add your desired search paths.
- The locator scheme (if it exists) you wish to look for assets in.
- The base url (used in generating URLs to assets).
- The base path (used in trimming the absolute path returned by the locator).

```php
<?php

use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\Assets\Assets;

$basePath = __DIR__;
$baseUrl = 'https://assets.userfrosting.com/';
$locator = new ResourceLocator($basePath);
$locator->registerStream('assets', '', [
    'owls/assets',
    'hawks/assets'
]);

$assets = new Assets($locator, 'assets', $baseUrl);
```

> Optionally 1 more argument can be passed into the `Assets` constructor, an instance of `PrefixTransformer`.
>
> Have a look at UserFrosting in dev mode to see this in action!

### Asset Bundles

To access asset bundles from an `Assets` instance, it must first be passed an instance of `AssetBundlesInterface` via `addAssetBundles`. The following example demonstates how to integrate with `@userfrosting/gulp-bundle-assets` (and by extension `gulp-bundle-assets`). Note that raw bundles are only supported when their configuration is defined as JSON.

*Directory Tree*

```txt
/
├build/
│ └asset-bundles.json
└public/
  └index.php

```

*build/asset-bundles.json*

```json
{
  "bundle": {
    "js/main": {
      "scripts": [
        "js/bootstrap-3.3.1.js",
        "js/crud.js"
      ]
    },
    "css/main": {
      "styles": [
        "vendor/font-awesome-4.5.0/css/font-awesome.css",
        "css/bootstrap-3.3.1.css",
        "css/bootstrap-custom.css",
        "css/paper.css"
      ]
    }
  }
}
```

*public/index.php*

```php
use UserFrosting\Assets\GulpBundleAssetsRawBundles;

// Load asset bundles.
$assetBundles = new GulpBundleAssetsRawBundles('../build/asset-bundles.json');

// Send loaded asset bundles to Assets instance.
$assets->addAssetBundles($assetBundles);

// Grab an asset bundle.
$assets->getJsBundleAssets('js/main');
// Outputs ["js/bootstrap-3.3.1.js", "js/crud.js"]
```

Compiled bundles can be used in much the same way, except using `GulpBundleAssetsCompiledBundles` and the bundlers result file.

If using the original `gulp-bundle-assets` you'll need to include an additional setting `options->result->type->styles/scripts="plain"` in each bundle.

### The Template Plugin

The template plugin is easy initialized by giving it the `Assets` instance, and simply gets passed into the templating engine environment of your choice to be used.

```php
use UserFrosting\Assets\AssetsTemplatePlugin;

$assetsPlugin = new AssetsTemplatePlugin($assets);

// Some code that passes it to Twig rendering environment.
```

```twig
{# Gets replaced at runtime with the following. Additional argument is optional. #}
{{ assets.js("js/main", { defer: true }) }}
```

```html
<script src="https://assets.userfrosting.com/assets/bootstrap/js/bootstrap.js" defer="true"></script>
<script src="https://assets.userfrosting.com/assets/bootstrap/js/npm.js" defer="true"></script>
```

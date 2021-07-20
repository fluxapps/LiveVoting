# srag/librariesnamespacechanger Library

Change namespace of libraries to plugin namespace

This project is licensed under the GPL-3.0-only license

## Usage

### Composer

First add the following to your `composer.json` file:

```json
"require": {
    "srag/librariesnamespacechanger": ">=0.1.0"
},
"config": {
    "optimize-autoloader": true,
    "sort-packages": true,
    "classmap-authoritative": true
},
"scripts": {
    "pre-autoload-dump": "srag\\LibrariesNamespaceChanger\\LibrariesNamespaceChanger::rewriteLibrariesNamespaces"
}
```

The optimized composer autoload is mandatory otherwise it will not work.

This script will change the namespace of the libraries on dump-autoload to a plugin specific namespace.

For instance the Library `DIC` and the the plugin `HelpMe`, the base namespace is `srag\DIC\HelpMe\`.

So you have to adjust it's namespaces in your code such in `classes` or `src` folder. You can use the replace feature of your IDE.

So you can force to use your libraries classes in the `vendor` folder of your plugin and come not in conflict to other plugins with different library versions and you don't need to adjust your plugins to newer library versions until you run `composer update` on your plugin.

It support the follow libraries:

* [srag libraries](https://packagist.org/packages/srag)

### In code

```php
...
use srag\LibrariesNamespaceChanger\x\LibrariesNamespaceChanger; 
...
LibrariesNamespaceChanger::getInstance()->doRewriteLibrariesNamespaces(string $project_root)
...
```

## Requirements

* PHP >=7.2

Change the namespace of the libraries on dump-autoload to a plugin specific namespace

### Usage

#### Composer
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

### PHP72Backport
If your plugin needs a PHP 7.0 compatible of version of a PHP 7.2/7.1 library, you can also add additionally the follow composer script:
```json
 "pre-autoload-dump": [
    ...,
      "srag\\LibrariesNamespaceChanger\\PHP72Backport::PHP72Backport"
    ]
```

It works with RegExp and affects your whole plugin workspace (`classes`, `src`, `vendor`, ...)

### php7backport
If your plugin needs a PHP 5.6 compatible of version of a PHP 7.0 library, you can also add additionally the follow composer script:
```json
 "post-update-cmd": "srag\\LibrariesNamespaceChanger\\PHP7Backport::PHP7Backport"
```

It uses the https://github.com/ondrejbouda/php7backport.git repo, but provides it as a composer script and patches it, amongst other things, it fix interfaces

### Requirements
* PHP >=7.0

### Adjustment suggestions
* External users can report suggestions and bugs at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_LNAMESPACECHANGER
* Adjustment suggestions by pull requests via github
* Customer of studer + raimann ag: 
	* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LNAMESPACECHANGER
	* Bug reports under https://jira.studer-raimann.ch/projects/LNAMESPACECHANGER

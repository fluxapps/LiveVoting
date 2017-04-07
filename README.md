# LiveVoting

## Features

- multiple/single/correct order/priorisation choice vote
- anonymous voting
- live updates
- pin acces via http://iliasdomain.tdl/vote
- vote and unvote
- show/hide live results
- Freeze Voting
- Fullscreen
 
## Documentation

https://github.com/studer-raimann/LiveVoting/blob/master/doc/Documentation.pdf
 
## Installation

Start at your ILIAS root directory  

```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject  
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/LiveVoting.git
cd ./LiveVoting
php composer.phar install
```  
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.  

## Shortlink-Config

Config Rewrite Rule in .htaccess or Apache-Config:

```apacheconf
<IfModule mod_rewrite.c>
	RewriteRule ^vote(/[\w]*|) Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?pin=$1 [L]
</IfModule>
```

## Browser-Support

LiveVoting is currently not fully supported by Google Chrome.

## HINWEIS

Dieses Plugin wird open source durch die studer + raimann ag der ILIAS Community zur Verüfgung gestellt. Das Plugin hat noch keinen Pluginpaten. Das heisst, dass die studer + raimann ag etwaige Fehler, Support und Release-Pflege für die Kunden der studer + raimann ag mit einem entsprechenden Hosting/Wartungsvertrag leistet. Wir veröffentlichen unsere Plugins, weil wir diese sehr gerne auch anderen Community-Mitglieder verfügbar machen möchten. Falls Sie nicht zu unseren Hosting-Kunden gehören, bitten wir Sie um Verständnis, dass wir leider weder kostenlosen Support noch die Release-Pflege für Sie garantieren können.
Sind Sie interessiert an einer Plugin-Patenschaft (https://studer-raimann.ch/produkte/ilias-plugins/plugin-patenschaften/ ) Rufen Sie uns an oder senden Sie uns eine E-Mail.

## Contact

studer + raimann ag  
Farbweg 9  
3400 Burgdorf  
Switzerland

info@studer-raimann.ch  
www.studer-raimann.ch 



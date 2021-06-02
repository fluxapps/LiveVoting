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
- Presenter link
- Export PowerPoint with slides for each questions with presenter link
- Presentation of Number range
 
## Documentation

https://github.com/studer-raimann/LiveVoting/blob/master/doc/Documentation.pdf
 
## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/LiveVoting.git LiveVoting
```
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.  

## Shortlink-Config

Config Rewrite Rule in .htaccess or Apache-Config:

```apacheconf
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^/?vote(/\w*)? /Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?xlvo_pin=$1 [L]
	RewriteRule ^/?presenter(/\w*)(/\w*)(/\w*)?(/\w*)? /Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/presenter.php?xlvo_pin=$1&xlvo_puk=$2&xlvo_voting=$3&xlvo_ppt=$4 [L]
</IfModule>
```

## PowerPoint export
For display the exported PowerPoint files you need to install the WebViewer-AddIn in PowerPoint:
https://appsource.microsoft.com/en-us/product/office/WA104295828?tab=Overview
You need also to configure your website as HTTPS and allow that your website can be displayed in frames.

### Requirements
* ILIAS 6.x / 7.x

### Adjustment suggestions
* Adjustment suggestions by pull requests
* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/PLLV__
* Bug reports under https://jira.studer-raimann.ch/projects/PLLV__
* For external users you can report it at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_PLLV

### ILIAS Plugin SLA
Wir lieben und leben die Philosophie von Open Source Software! Die meisten unserer Entwicklungen, welche wir im Kundenauftrag oder in Eigenleistung entwickeln, stellen wir öffentlich allen Interessierten kostenlos unter https://github.com/studer-raimann zur Verfügung.

Setzen Sie eines unserer Plugins professionell ein? Sichern Sie sich mittels SLA die termingerechte Verfügbarkeit dieses Plugins auch für die kommenden ILIAS Versionen. Informieren Sie sich hierzu unter https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Bitte beachten Sie, dass wir nur Institutionen, welche ein SLA abschliessen Unterstützung und Release-Pflege garantieren.

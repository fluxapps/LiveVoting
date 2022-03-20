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

https://github.com/fluxapps/LiveVoting/blob/master/doc/Documentation.pdf
 
## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/fluxapps/LiveVoting.git LiveVoting
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

## Contributing :purple_heart:
Please ...
1. ... register an account at https://git.fluxlabs.ch
2. ... create pull requests :fire:

## Adjustment suggestions / bug reporting :feet:
Please ...
1. ... register an account at https://git.fluxlabs.ch
2. ... ask us for a Service Level Agreement: support@fluxlabs.ch :kissing_heart:
3. ... Read and create issues

# LiveVoting

![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/fluxapps/livevoting?style=flat-square)
![GitHub closed issues](https://img.shields.io/github/issues-closed/fluxapps/livevoting?style=flat-square&color=success)
[![GitHub issues](https://img.shields.io/github/issues/fluxapps/livevoting?style=flat-square&color=yellow)](https://github.com/fluxapps/livevoting/issues)
![GitHub closed pull requests](https://img.shields.io/github/issues-pr-closed/fluxapps/livevoting?style=flat-square&color=success)
![GitHub pull requests](https://img.shields.io/github/issues-pr/fluxapps/livevoting?style=flat-square&color=yellow)
[![GitHub forks](https://img.shields.io/github/forks/fluxapps/livevoting?style=flat-square&color=blueviolet)](https://github.com/fluxapps/livevoting/network)
[![GitHub stars](https://img.shields.io/github/stars/fluxapps/livevoting?style=flat-square&color=blueviolet)](https://github.com/fluxapps/livevoting/stargazers)
[![GitHub license](https://img.shields.io/github/license/fluxapps/livevoting?style=flat-square)](https://github.com/fluxapps/livevoting/blob/main/LICENSE.md)


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

## Maintenance

fluxlabs ag, support@fluxlabs.ch

This project is maintained by fluxlabs.

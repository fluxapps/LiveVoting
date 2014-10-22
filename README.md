LiveVoting
==========
###Features
- multiple/single choice vote
- anonymous voting
- live updates
- pin acces via http://iliasdomain.tdl/vote
- vote and unvote
- show/hide live results
- Freeze Voting
- Fullscreen
- SMS Voting (needs a PhoneNumber and Account from studer + raimann ag)

###Installation
Start at your ILIAS root directory  
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject  
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/LiveVoting.git  
```  
As ILIAS administrator go to "Administration->Plugins" and install/activate the plugin.  

###Shortlink-Config
- Config Rewrite Rule in .htaccess or Apache-Config:

<IfModule mod_rewrite.c>
	RewriteEngine on
	# Notes:
	# - don't match something like "/votetest.php?pin=23"
	# - use \? to mask question mark, because it's special in regular expressions
	RewriteRule ^vote$ Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php [L]
	RewriteRule ^vote\?(.*)$ Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/pin.php?$1 [L]
</IfModule>


###Contact
studer + raimann ag  
Waldeggstrasse 72  
3097 Liebefeld  
Switzerland  

info@studer-raimann.ch  
www.studer-raimann.ch



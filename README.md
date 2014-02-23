# Raspcontrol

Raspcontrol is a web control centre written in PHP for Raspberry Pi.

![Home of Raspcontrol](raspcontrol-home.png)

***


## Installation

__The [Installation Guide](https://github.com/Bioshox/Raspcontrol/wiki/Installation-Guide) in now hosted on the wiki.__  
For the moment, you need a web server (like Apache) installed on your Raspberry Pi.


If you are in a hurry, just clone the repository with:

	git clone https://github.com/Bioshox/Raspcontrol.git raspcontrol

And create the json authentifation file `/etc/raspcontrol/database.aptmnt` with 740 rights and owned by www-data:

	{
 	   "user":       "yourName",
 	   "password":   "yourPassword"
	}

## Optional configuration

In order to have some beautiful URLs, you can enable URL Rewriting.  
Please follow instructions described on the wiki page [Enable URL Rewriting](https://github.com/Bioshox/Raspcontrol/wiki/Enable-URL-Rewriting).

__Note:__ It's not necessary to enable URL Rewriting to use Raspcontrol.


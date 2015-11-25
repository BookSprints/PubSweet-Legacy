PubSweet
========

An experimental framework for accelerated collaborative knowledge production. Its rough but ready. Contributions welcome but you might wish to put your energy into PubSweet2 (currently being uploaded to gh) which is a more modular approach.

Requirements
============

PHP version 5.5 or newer.
Enabled php mods: ctype, mysqli
MySQL 5.0.51a or newer.
Nodejs server is optional.

Installation
============

PubSweet Ver 1 install

Install Apache/PHP/MySQL environment (e.g. http://www.ampps.com)

[Download PubSweet source](https://github.com/BookSprints/PubSweet/archive/master.zip) and unzip it in the webroot

			
Change permissions of directory and contents to apache2 user

	chown -R www-data:www-data /var/www/pubsweet/
	
Change permissions of directory where epub files will 

	chmod 755 /var/www/pubsweet/application/epub/

enable the mod_rewrite apache module

	a2enmod rewrite

The command enables the module, or, if it is already enabled, displays the words, "Module rewrite already
enabled", if it was not enabled, it will enabled it, then you need to run “service apache2 restart” to activate it


Alter config file

	Set your base URL

		nano /var/www/html/pubsweet/application/config/config.php

		change
			$config['base_url']   = 'http://pubsweet.local';
		to
			$config['base_url']     = 'http://IPno/pubsweet';
		or
			$config['base_url']     = 'http://YourWEBaddress/pubsweet';

			IPno : is the Ip number of your server
			YourWEBaddress : the web address of your server

		change
			$config['index_page'] = '';
			//$config['index_page'] = 'index.php';
		to
			//$config['index_page'] = '';
			$config['index_page'] = 'index.php';

Go to `http://YourInstallation/install/` and follow the instructions. After executing the installation remove the /install/ folder

Go to application/config/pubsweet.php to enable/disable the use of ssl.

In your favorite browser, got to your new PubSweet site

Broadcaster installation (optional)
===================

Install [nodejs](http://nodejs.org/download/) 

In the "broadcaster" folder, run this:

1. `npm install`
2.a `node task-app.js &`
2.b `node app-ssl.js &` if you are going to use the ssl version
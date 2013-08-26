FastBackup
==========

Enables Backup of several systems like TYPO3, WBB and MediaWiki. Is it pretty easy to add your own service and it is also possible to run this as cron if you need to backup instances every night.

What it does
------------

This little script gives you the possibility to quickly backup different PHP based Applications. It fetches the Database informations from the config file and builds quickly tar files out of the hosts files and the database. It is perfect if you dislike looking on the config file to get the right database informations 

**Warning:** *Read carefully if the database is correct if you are using one config file with configuration for production and development context.*


How to use it
-------------

    $ ./backup.php SYSTEM ORIGIN TARGET

### Params

##### System

System is the lowercase written system you want to backup. Take a look at __Working Systems__ to get an overview what currently exists.

##### Origin

This is the location where the system you want to backup is located. For example `/var/www/domain.tld/htdocs`.

##### Target

This is the location where the tar.gz should be located. For example it could be `./myBackup.tar.gz`


Working Systems
---------------

### TYPO3

Creates a backup of TYPO3's database and complete filesystem stored under `ORIGIN`.

### MediaWiki

Creates a backup of MediaWiki's database and complete filesystem stored under `ORIGIN`.

### Woltlabs Burning Board ( WBB )

Creates a backup of WBB's database and complete filesystem stored under `ORIGIN`.

### Wordpress

Creates a backup of Wordpress' database and complete filesystem stored under `ORIGIN`.

Frequently answered Questions
-----------------------------

### Script is backing up wrong database

As the script is a CLI script you propably created an if condition based on the hostname. This hostname is not given if you run the script on command line. To help you in this case I need some of your configuration code, please remove passwords before sending me emails.

Possible Addons
---------------

Currently nothing of this is realy planned at least as long as nobody needs it. If you need it open a feature request =).

* Remove temp and cached Files from repository and database before writing tar
* Allow forced backup ( Currently if backup file already exists the backup will not work )

How to contribute
-----------------
The TYPO3 Community lives from your contribution!

You wrote a feature? - Start a pull request!

You found a bug? - Write an issue report!

You are in the need of a feature? - Write a feature request!

You have a problem with the usage? - Ask!

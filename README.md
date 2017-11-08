# Epoch System Builder

## Demo ##

[epoch.epitelinc.com](http://epoch.epitelinc.com/)

## Setup ##

For security, place *config.ini* outsite of browseable files and change the path references in *index.php* accordingly.
```
$config_file = '../config.ini';
```

## Files ##

* *index.php* contains the html, db connection, dropdown values and quote results
* *utils.php* contains the php functions
* *quote.php* contains the Quote class
* *quoteitem.php* contains the QuoteItem class
* *gains.php* generates the gains SQL that was used to populate the database using *create_database.sql*

## Data ##

dropdown value db tables: dac, system, animal, biopotential, channels, transmitter_gain, duration

many-to-many part-number db tables: receiver, transmitter, gains

activator and cable part-numbers are in the php code

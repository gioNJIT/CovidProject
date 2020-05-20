# CovidProject


Stats.sh will open sources.txt and download the html files.

It will also use tag soup (which needs to be in the same folder) to change the html to xhtml.

It then calls parser.py to parse the relevant data and place the data into a mysql database.

After the data is stored, it deletes both xhtml and html files.

you can now open covid.php in your browser and the data will be shown like it is in the screen shots

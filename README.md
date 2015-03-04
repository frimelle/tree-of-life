<h>Wikidata's Tree Of Life</h>
========

Right now, it's not working properly/at all. Better use the biota branch until this one is working again. 

Uses jstree (http://www.jstree.com/).
Download jstree and unzip the dist folder. 

You will need MYSQLdb (python) for Tree.py and curl (php) for data.php.

To download a wikidata dump use the download.py file. (You might want to change the date to the most recent dump there is. https://dumps.wikimedia.org/other/wikidata/)

Use Tree.py to populate the database.

Demo: https://tools.wmflabs.org/tree-of-life/

The branch 'biota' shows the tree with a single root node (biota).

TODO: 

- already for the root nodes, data.php needs to long to get them all from the database (too many nodes)
- works only if memory limit is manipulated (not cool)
- handle places where sql injections are possible now!
- investigate on the errors in data.php
- refactor: 
	- refactor data.php
- include api requests again
- hasChildren in or out?
	


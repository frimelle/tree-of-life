<h>Wikidata's Tree Of Life</h>
========

Uses jstree (http://www.jstree.com/).
Download jstree and unzip the dist folder. 

To download a wikidata dump use the download.py file. (You might want to change the date to the most recent dump there is. https://dumps.wikimedia.org/other/wikidata/)

Use Tree.py to populate the database.

Demo: https://tools.wmflabs.org/tree-of-life/

The branch 'biota' shows the tree with a single root node (biota).

TODO: 

- refactor: 
	- javascript in own file
	- refactor Tree.py
- more than one tree, show every root node and tree we have so far in Wikidata
	- edit database
	- edit Tree.py
	- and data.php and the javascript part


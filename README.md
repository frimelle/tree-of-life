<h>Wikidata's Tree Of Life</h>
========

Uses jstree (http://www.jstree.com/).
Download jstree and unzip the dist folder. 

To download a wikidata dump use the download.py file. (You might want to change the date to the most recent dump there is. https://dumps.wikimedia.org/other/wikidata/)

Use Tree.py to populate the database. There are two versions of the Tree file. Tree.py takes longer but only writes nodes in the database, that are really needed and has a field (hasChildren), that indicates, which nodes actually have children. I'd recommend to use Tree.py.
Tree-quick.py is quicker, but adds more nodes to the database (including the one, which are not in the biota tree).

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


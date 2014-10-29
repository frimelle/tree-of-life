# This Python file uses the following encoding: utf-8
#!/usr/bin/python
import gzip 
import json
import MySQLdb

__author__ = "Lucie-Aimée Kaffee"
__email__ = "lucie.kaffee@gmail.com"
__license__ = "GNU GPL v2+"

db = MySQLdb.connect(host="localhost", 
		     user="root", 
                     passwd="password", 
                     db="tree_db",
                     use_unicode=True, 
                     charset="utf8")


#put this in another file- learn about python's import!! 
class Node(object):
    def __init__(self, data, name):
        self.data = data
        self.name = name
        self.children = []

    def add_child(self, obj):
        self.children.append(obj)
       #print 'child added'

counter_no_some = 0
counter_nodes = 0
counter_roots = 0
counter_in_tree = 0

def isInstanceOfTaxon(json_l):
	#check if the things we will look for are actually there (otherwise there are errors, e.g. when there is no datavalue attribute)
		if 'claims' in json_l and 'P31' in json_l['claims'] and 'datavalue' in json_l['claims']['P31'][0]['mainsnak']:
			#check if instance of (P31) taxon (Q16521):
			instanceOf_id = json_l['claims']['P31'][0]['mainsnak']['datavalue']['value']['numeric-id']
			if instanceOf_id == 16521: 
				return True
		else:
			return False

def hasParentTaxon(json_l):
	#check if there is a parent taxon (P171) for this item
	if 'P171' in json_l['claims']:
		if 'datavalue' in json_l['claims']['P171'][0]['mainsnak']:
			return True
	else:
		return False

#never used
def hasSubclassOf(json_l):
	if 'P279' in json_l['claims'] and 'datavalue' in json_l['claims']['P279'][0]['mainsnak']:
		return True

def hasNoSomeValueParent(json_l):
	if 'P171' in json_l['claims'] and not 'datavalue' in json_l['claims']['P171'][0]['mainsnak']:
		return True

def hasEnLabel(json_l):
	if 'en' in json_l['labels']:
		if 'value' in json_l['labels']['en']:
			return True
	else:
		return False

#for debugging
def printChildren(parent):
	x = 0
	print "parent taxon: "
	print parent.data
	print ' --------------------------------'
	print "children: "
	for c in parent.children:
		print c.data
		x = x + 1	
	print ' --------------------------------'
	print x 
	print ' --------------------------------'


def writeDB( child, name, parent, hasChildren ):
	cur = db.cursor() 
	cur.execute("""INSERT INTO node (child, name, parent, hasChildren) VALUES (%s, %s, %s, %s);""", (child, name, parent, hasChildren))
	db.commit()

in_db = []
def traversing( node ):
	for c in node.children:
		hasChildren = False
		if c.children:
			hasChildren = True
		if c.data not in in_db: 
			writeDB(c.data, c.name, node.data, hasChildren)   #here I am writing the things to the database
			in_db.append(c.data)
		traversing( c )
		global counter_in_tree
		counter_in_tree = counter_in_tree + 1


#here starts the actual important stuff
f = gzip.open('wikidump.json.gz')
node_dict = {} #list of the nodes already added to the tree
root_array = []
no_some_value_node = Node('NoSomeValue', "")
node_dict['no_some'] = no_some_value_node
for line in f:

	line = line.rstrip().rstrip(',')
	try:
		json_l = json.loads(line)
	except ValueError, e:
		continue

	if isInstanceOfTaxon(json_l):
		child_id = json_l['id']
		
		if node_dict.has_key(child_id):
			child = node_dict[child_id]
			if child.name == "":
				if hasEnLabel(json_l):
					name = json_l['labels']['en']['value']
					name = name
				else:
					name = ""
				child.name = name;
		else:
			if hasEnLabel(json_l):
				name = json_l['labels']['en']['value']
				name = name
			else:
				name = ""
			child = Node(child_id, name)
			node_dict[child_id] = child
			counter_nodes = counter_nodes + 1
	
		#check if there is a parent taxon (P171) for this item or if it is a subclass of antother taxon
		if hasParentTaxon(json_l):
			if hasParentTaxon(json_l):
				parent_id = 'Q' + str(json_l['claims']['P171'][0]['mainsnak']['datavalue']['value']['numeric-id'])

			if node_dict.has_key(parent_id):
				parent = node_dict[parent_id]
			else:
				name = ""
				parent = Node(parent_id, name)
				node_dict[parent_id] = parent
				counter_nodes = counter_nodes + 1
					
			parent.add_child(child)
			
		
		elif hasNoSomeValueParent(json_l):
			no_some_value_node.add_child(child)
			counter_no_some = counter_no_some + 1

		else: 
			root_array.append(child)
			counter_roots = counter_roots + 1

count_r_ch = 0


for r in root_array:
	#start building the actual tree (and writing in the database)
	if r.data == 'Q2382443':
		traversing(r)
	if r.children:
		count_r_ch = count_r_ch + 1


print "Nodes: " + str(counter_nodes)
print "Roots: " + str(counter_roots)
print "Roots, excluding roots without children: " + str(count_r_ch)
print "Nodes with parent taxon no or some value: " + str(counter_no_some)
print "Nodes in Biota Tree: " + str(counter_in_tree)

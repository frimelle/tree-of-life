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

def hasEnLabel(json_l):
	if 'en' in json_l['labels']:
		if 'value' in json_l['labels']['en']:
			return True
	else:
		return False


def writeDB( child, name, parent ):
	cur = db.cursor() 
	cur.execute("""INSERT INTO node (child, name, parent) VALUES (%s, %s, %s);""", (child, name, parent))
	db.commit()


#here starts the actual important stuff
f = gzip.open('wikidump.json.gz')
for line in f:
	line = line.rstrip().rstrip(',')
	try:
		json_l = json.loads(line)
	except ValueError, e:
		continue

	if isInstanceOfTaxon(json_l):

		child_id = json_l['id']
		if child_id is 'Q10750326':
			print line
			break
		if hasEnLabel(json_l):
			name = json_l['labels']['en']['value']
			name = unicode(name)
		else:
			name = ""
	
		#check if there is a parent taxon (P171) for this item or if it is a subclass of antother taxon
		if hasParentTaxon(json_l): #or hasSubclassOf(json_l):
			if hasParentTaxon(json_l):
				parent_id = 'Q' + str(json_l['claims']['P171'][0]['mainsnak']['datavalue']['value']['numeric-id'])

		#writeDB(child_id, name, parent_id)
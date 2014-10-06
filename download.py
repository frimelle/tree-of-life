import urllib

#TODO: make this a class with a method. give the metod the date we want to see. check if the link exists before starting the download

def urlExists(url):
	try:
		f = urllib2.urlopener(urllib2.Request(url))
		return true


wikidump = urllib.URLopener()
wikidump.retrieve('http://dumps.wikimedia.org/other/wikidata/20140721.json.gz', 'wikidump.json.gz')
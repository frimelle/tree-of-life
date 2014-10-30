import urllib

#doesn't check if the link actually exists
date = '20141027'

wikidump = urllib.URLopener()
wikidump.retrieve('http://dumps.wikimedia.org/other/wikidata/' + date + '.json.gz', 'wikidump.json.gz')
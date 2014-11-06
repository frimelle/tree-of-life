<?php
	
	$url_labels = 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q42&props=labels&format=json';
	$url_urls = 'https://www.wikidata.org/w/api.php?action=wbgetentities&props=sitelinks/urls&ids=Q42&format=json';
	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url_labels,
	    CURLOPT_USERAGENT => 'tree-of-life'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	$content = json_decode($resp, true);
	print_r($content['entities']['Q42']['labels']['en']['value']);


	//now for the links
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url_urls,
	    CURLOPT_USERAGENT => 'tree-of-life'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	$content = json_decode($resp, true);
	print_r($content['entities']['Q42']['sitelinks']['enwiki']['url']);


?>

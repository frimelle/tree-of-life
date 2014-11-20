<?php
	function curl_start( $url ) {
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array( $curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERAGENT => 'tree-of-life'
		));
		// Send the request & save response to $resp
		$resp = curl_exec( $curl );
		// Close request to clear up some resources
		curl_close( $curl );
		return $resp; 
	}

	function get_label_url( $entity_ids, $lang ) {
		$ids = implode("|", $entity_ids);
		$url = 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=' . $ids . '&props=sitelinks/urls|labels&languages='. $lang . '&format=json';	
		$content = json_decode( curl_start( $url ), true );
		return $content; 
	}


	//function is not used
	function get_label( $entity_id, $lang ){
		$url_label = 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=' . $entity_id . '&props=labels&format=json';
		$content = json_decode( curl_start( $url_label ), true );
		$label = "";
		if ( array_key_exists( $lang, $content['entities'][$entity_id]['labels'] ) && array_key_exists( 'value', $content['entities'][$entity_id]['labels'][$lang] ) ) {
			$label = $content['entities'][$entity_id]['labels'][$lang]['value'];

		}
		return $label;
	}

	//function is not used
	function get_wiki_url( $entity_id, $lang ) {
		$url = 'https://www.wikidata.org/w/api.php?action=wbgetentities&props=sitelinks/urls&ids=' . $entity_id . '&format=json';
		$content = json_decode(curl_start($url), true);
		$url_wikipedia = ""; 
		if ( array_key_exists( $lang . 'wiki', $content['entities'][$entity_id]['sitelinks'] ) && array_key_exists( 'url', $content['entities'][$entity_id]['sitelinks'][$lang . 'wiki'] ) ) {
			$url_wikipedia = $content['entities'][$entity_id]['sitelinks'][$lang . 'wiki']['url'];
		}
		return $url_wikipedia;
	}

	#$ids = array('Q42', 'Q10358398', 'Q8', 'Q8486');
	#print_r(get_label_url($ids, 'en'));
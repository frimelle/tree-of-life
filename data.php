<?php

/**
 * gets the data from the database needed for the tree
 * @licence GNU GPL v2+
 * @author Lucie-Aimée Kaffee
 **/

	require_once('api-request.php');

	$db = mysqli_connect( "localhost", "root", "password", "tree_db" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}
	
	session_start();
	if( !isset($_SESSION['lang'])) {
		$lang = 'en';
	} else {
		$lang = $_SESSION['lang'];
	}

	////////////////for testing stuff
	//$_GET['entity_id'] = 'Q1';
	//////////////////////////////

	function getDataArray($nodes, $ids, $entity_id, $has_children ) {
		$new_node = array();

		array_push($ids, $entity_id);

		$link = 'https://m.wikidata.org/wiki/' . $entity_id;

		$new_node['text'] = $entity_id;

		if ($has_children = 1) {
			$new_node['children'] = true; 
		} else {
			$new_node['children'] = false;
		}

		$new_node['id'] = $entity_id;
		$a_attr_elements['href'] = $link;
		$new_node['a_attr'] = $a_attr_elements;
		array_push($nodes, $new_node);
		return $nodes;

	}

	function chunkArray($content, $ids, $lang) {
		$id_arrays = array_chunk( $ids, 49 );
		$contents = array();

		foreach( $id_arrays as $ids ) {
			array_push( $contents, get_label_url( $ids, $lang ) );
		}
		$content = array_merge_recursive($contents);
		return $content;
	}

	#check if this is the first node, if it is, use the root
	if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {

		$qstring_root= "SELECT * FROM node WHERE isRoot=true";
		$qresult_root = mysqli_query( $db, $qstring_root );
		$roots = array();
		$ids = array();

		while( $row = mysqli_fetch_object( $qresult_root ) ) {
			
			$entity_id = $row->id;
			$has_children = $row->hasChildren;

			$roots = getDataArray($roots, $ids, $entity_id, $has_children);
		}

		$content = array();

		#check size of id Array for the api request (should be less than 50)
		if( sizeof($ids) < 50) {
			$content = get_label_url( $ids, $lang );
		} else {
			$content = chunkArray($content, $ids, $lang);
		}

		### REFACTOR ME! PUT ME IN A FUNCTION!
		for ( $i = 0; $i < sizeof($roots); $i++ ) {
			if( array_key_exists( 'labels', $content['entities'][$roots[$i]['id']] ) ) {
				if ( array_key_exists( $lang, $content['entities'][$roots[$i]['id']]['labels'] ) && array_key_exists( 'value', $content['entities'][$roots[$i]['id']]['labels'][$lang] ) ) {
					
					$roots[$i]['text'] = $content['entities'][$roots[$i]['id']]['labels'][$lang]['value'];
				}
			}

			if(array_key_exists('sitelinks', $content['entities'][$roots[$i]['id']])) {
				if ( array_key_exists( $lang . 'wiki', $content['entities'][$roots[$i]['id']]['sitelinks'] ) && array_key_exists( 'url', $content['entities'][$roots[$i]['id']]['sitelinks'][$lang . 'wiki'] ) ) {
					
					$roots[$i]['a_attr']['href'] = $content['entities'][$roots[$i]['id']]['sitelinks'][$lang . 'wiki']['url'] . '?useskin=mobil&mobileaction=toggle_view_mobile';
				}
			}
		}
		
		echo json_encode($roots);

	} else {
		#get the entity_id of the node that was clicked
		$parent = $_GET['entity_id'];
		$qstring = "SELECT * FROM node WHERE parent='$parent'";
		$qresult = mysqli_query( $db, $qstring );
		$nodes = array();
		$ids = array();

		while( $row = mysqli_fetch_object( $qresult ) ) {
			
			$entity_id = $row->id;
			$has_children = $row->hasChildren;

			$nodes = getDataArray($nodes, $ids, $entity_id, $has_children);
		}

		$content = array();

		#check size of id Array for the api request (should be less than 50)
		if( sizeof($ids) < 50) {
			$content = get_label_url( $ids, $lang );
		} else {
			$content = chunkArray($content, $ids, $lang);
		}

		### REFACTOR ME! PUT ME IN A FUNCTION!
		for ( $i = 0; $i < sizeof($nodes); $i++ ) {
			if( array_key_exists( 'labels', $content['entities'][$nodes[$i]['id']] ) ) {
				if ( array_key_exists( $lang, $content['entities'][$nodes[$i]['id']]['labels'] ) && array_key_exists( 'value', $content['entities'][$nodes[$i]['id']]['labels'][$lang] ) ) {
					
					$nodes[$i]['text'] = $content['entities'][$nodes[$i]['id']]['labels'][$lang]['value'];
				}
			}

			if(array_key_exists('sitelinks', $content['entities'][$nodes[$i]['id']])) {
				if ( array_key_exists( $lang . 'wiki', $content['entities'][$nodes[$i]['id']]['sitelinks'] ) && array_key_exists( 'url', $content['entities'][$nodes[$i]['id']]['sitelinks'][$lang . 'wiki'] ) ) {
					
					$nodes[$i]['a_attr']['href'] = $content['entities'][$nodes[$i]['id']]['sitelinks'][$lang . 'wiki']['url'] . '?useskin=mobil&mobileaction=toggle_view_mobile';
				}
			}
		}
		
		echo json_encode($nodes);
	}
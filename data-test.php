<?php

/**
 * only temoporary, WIP file. Shouldn't be used and deleted as soon as data.php is ready.
 * gets the data from the database needed for the tree
 * @licence GNU GPL v2+
 * @author Lucie-Aimée Kaffee
 **/

	require_once('api-request.php');

	$db = mysqli_connect( "localhost", "root", "password", "tree_db2" );
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
	//$_GET['entity_id'] = 'Q1002125';
	//////////////////////////////


	$root = 'Q2382443';
	$root_name_default = 'biota';
	$root_link_default = 'https://m.wikidata.org/wiki/' . $root;


	#check if this is the first node, if it is, use the root
	if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {

		$qstring_root= "SELECT * FROM node WHERE hasRoot=true";


		while( $row = mysqli_fetch_object( $qresult ) ) {
			
			$entity_id = $row->id;
			$parent = $row->parent;
			$has_children = $row->hasChildren; 


		//ALTER TEIL FÄNG HIER AN- NICHT DIE KLAMMERN VERGESSEN BEIM LÖSCHEN!
		$root_name = get_label($root, $lang);
		
		if ($root_name == "") {
			$root_name = $root_name_default;
		} 
		$root_link = get_wiki_url($root, $lang) . '?useskin=mobil&mobileaction=toggle_view_mobile';
		if ($root_link == "") {
			$root_link = $root_name_default;
		} 

		echo '[{"text":"' . $root_name . '","children":true, "id":"' . $root . '", "a_attr":{"href":"Q1"}}, {"text":"Test root","children":true, "id":"Q1", "a_attr":{"href":"https://wikidata.org"}}]';
	} else {
		#get the entity_id of the node that was clicked
		$parent = $_GET['entity_id'];
		$qstring = "SELECT * FROM node WHERE parent='$parent'";
		$qresult = mysqli_query( $db, $qstring );
		$new_child = array();
		$children = array();
		$ids = array();

		while( $row = mysqli_fetch_object( $qresult ) ) {
			
			$entity_id = $row->id;
			$parent = $row->parent;
			$has_children = $row->hasChildren; //this is onle for Tree.py not Tree-quick.php 

			array_push($ids, $entity_id);


			$link = 'https://m.wikidata.org/wiki/' . $entity_id;

			$new_child['text'] = $entity_id;

			if ($has_children = 1) {
				$new_child['children'] = true; 
			} else {
				$new_child['children'] = false;
			}
			$new_child['id'] = $entity_id;
			$a_attr_elements['href'] = $link;
			$new_child['a_attr'] = $a_attr_elements;
			array_push($children, $new_child);

		}

		$content = array();

		if( sizeof($ids) < 50) {
			$content = get_label_url( $ids, $lang );
		} else {

			$id_arrays = array_chunk( $ids, 49 );
			$contents = array();

			foreach( $id_arrays as $ids ) {
				array_push( $contents, get_label_url( $ids, $lang ) );
			}
			$content = array_merge_recursive($contents);

		}
		for ( $i = 0; $i < sizeof($children); $i++ ) {
			if( array_key_exists( 'labels', $content['entities'][$children[$i]['id']] ) ) {
				if ( array_key_exists( $lang, $content['entities'][$children[$i]['id']]['labels'] ) && array_key_exists( 'value', $content['entities'][$children[$i]['id']]['labels'][$lang] ) ) {
					
					$children[$i]['text'] = $content['entities'][$children[$i]['id']]['labels'][$lang]['value'];
				}
			}

			if(array_key_exists('sitelinks', $content['entities'][$children[$i]['id']])) {
				if ( array_key_exists( $lang . 'wiki', $content['entities'][$children[$i]['id']]['sitelinks'] ) && array_key_exists( 'url', $content['entities'][$children[$i]['id']]['sitelinks'][$lang . 'wiki'] ) ) {
					
					$children[$i]['a_attr']['href'] = $content['entities'][$children[$i]['id']]['sitelinks'][$lang . 'wiki']['url'] . '?useskin=mobil&mobileaction=toggle_view_mobile';
				}
			}
		}
		
		echo json_encode($children);
	}
<?php

/**
* gets the data from the database needed for the tree
* @licence GNU GPL v2+
* @author Lucie-Aimée Kaffee
**/

require_once('api-request.php');

////////////////for testing stuff
//$_GET['entity_id'] = 'Q10028';
//////////////////////////////

//start the session and actually return the json
sessionstart();
get_json();


//start a session to remember the language settings
function sessionstart() {
	session_start();
	if( !isset($_SESSION['lang'])) {
		$lang = 'en';
	} else {
		$lang = $_SESSION['lang'];
	}
}

//connect to the database.
function connect_db() {
	//ini_set('memory_limit', '-1');
	$db = mysqli_connect( "localhost", "root", "password", "tree_db" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}
	return $db;
}

//get the data from db and make it to array (needed for json) and encode to UTF8 (needed by json_encode)
function make_node_array( $name, $entity_id, $link ) {
	$new_node = array();
	$new_node['text'] = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($name));
	$new_node['children'] = true;
	$new_node['id'] = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($entity_id));
	//$a_attr_elements['href'] = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($link));
	//$new_node['a_attr'] = $a_attr_elements;

	return $new_node;
}

//get the data of all roots as an array
function get_root_data() {
	$db = connect_db();
	$qstring_root= "SELECT * FROM node WHERE isRoot=true";
	$qresult_root = mysqli_query( $db, $qstring_root );

	$roots = array();

	while( $row = mysqli_fetch_object( $qresult_root ) ) {

		$name = $row->name;
		if ( $name == "" ) {
			$name = $entity_id;
		}
		$entity_id = $row->id;
		//$link = 'https://m.wikidata.org/wiki/' . $entity_id;

		$new_root = make_node_array( $name, $entity_id, $link );
		array_push( $roots, $new_root );
	}
	return $roots;
}

//get the data from all nodes deriving from a certain parent node as array
function get_node_data() {
	$db = connect_db();
	//sql injection!!!!!!!!!!!!!!!!!!!!!!!!!!!
	$parent = $_GET['entity_id'];
	$qstring = "SELECT * FROM node WHERE parent='$parent'";
	$qresult = mysqli_query( $db, $qstring );

	$nodes = array();

	while( $row = mysqli_fetch_object( $qresult ) ) {

		$name = $row->name;
		//this if statement should be in some function!
		if ( $name == "" ) {
			$name = $entity_id;
		}
		$entity_id = $row->id;
		$link = 'https://m.wikidata.org/wiki/' . $entity_id;

		$new_node = make_node_array( $name, $entity_id, $link );
		array_push( $nodes, $new_node );
	}
	return $nodes;
}


//decide wether to load the nodes or the roots and encode them into json
function get_json() {
	if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {
		echo json_encode(get_root_data());
	} else {
		echo json_encode(get_node_data());
	}
}

<?php

/**
* gets the data from the database needed for the tree
* @licence GNU GPL v2+
* @author Lucie-Aimée Kaffee
**/

require_once('api-request.php');

////////////////for testing stuff
//$_GET['entity_id'] = 'Q1';
//////////////////////////////

sessionstart();
get_json();

function sessionstart() {
	session_start();
	if( !isset($_SESSION['lang'])) {
		$lang = 'en';
	} else {
		$lang = $_SESSION['lang'];
	}
}

function connect_db() {
	$db = mysqli_connect( "localhost", "root", "password", "tree_db2" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}
	return $db;
}

function make_node_array( $name, $entity_id, $link ) {
	$new_node = array();
	$new_node['text'] = $name;
	$new_node['children'] = true;
	$new_node['id'] = $entity_id;
	$a_attr_elements['href'] = $link;
	$new_node['a_attr'] = $a_attr_elements;

	return $new_node;
}

function get_root_json() {
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
		$link = 'https://m.wikidata.org/wiki/' . $entity_id;

		$new_root = make_node_array( $name, $entity_id, $link );
		array_push( $roots, $new_root );
	}
	return $roots;
}

function get_node_json() {
	$db = connect_db();
	$parent = $_GET['entity_id'];
	$qstring = "SELECT * FROM node WHERE parent='$parent'";
	$qresult = mysqli_query( $db, $qstring );

	$nodes = array();

	while( $row = mysqli_fetch_object( $qresult ) ) {

		$name = $row->name;
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

function get_json() {
	if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {
		echo json_encode(get_root_json());
	} else {
		echo json_encode(get_node_json());
	}
}

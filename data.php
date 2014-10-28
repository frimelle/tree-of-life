<?php

	$db = mysqli_connect( "localhost", "root", "password", "tree_db" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}

	$new_child = array();
	$root = 'Q2382443';
	$root_name = 'biota';
	$root_link = 'http://www.wikidata.org/wiki/' . $root;
	$children = array();
	if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {
		echo '[{"text":"' . $root_name . '","children":true, "id":"' . $root . '", "link":"' . $root_link . '"}]';
	} else {
		$parent = $_GET['entity_id'];
		$qstring = "SELECT * FROM node WHERE parent='$parent'";
		$qresult = mysqli_query( $db, $qstring );
		while( $row = mysqli_fetch_object( $qresult ) ) {
			$entity_id = $row->child;
			$name =  $row->name;
			$parent = $row->parent;
			$link = 'http://www.wikidata.org/wiki/' . $entity_id;
			if( $name != "" ) {
				$new_child['text'] = $name;
			} else {
				$new_child['text'] = $entity_id;
			}
			$new_child['children'] = true;
			$new_child['id'] = $entity_id;
			$a_attr_elements['href'] = $link;
			$new_child['a_attr'] = $a_attr_elements;
			array_push($children, $new_child);

		}
		echo json_encode($children);
	}
?>
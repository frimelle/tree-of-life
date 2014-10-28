<?php

	$db = mysqli_connect( "localhost", "root", "password", "tree_db" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}
	$post = $_POST['q_nr'];
	$get = $_GET['q_nr'];
	$qstring1 = "INSERT INTO post_get (post, get) VALUES ('$post', '$get')";
	mysqli_query( $db, $qstring1 );

	$post_data = array();
	$new_child = array();
	$root = 'Q2382443';
	$root_name = 'biota';
	$children = array();
	if (!isset($_GET['q_nr']) || $_GET['q_nr'] == '#') {
		echo '[{"text":"' . $root_name . '","children":true, "id":"' . $root . '"}]';
	} else {
		$parent = $_GET['q_nr'];
		$qstring = "SELECT * FROM node WHERE parent='$parent'";
		$qresult = mysqli_query( $db, $qstring );
		while( $row = mysqli_fetch_object( $qresult ) ) {
			$qnr = $row->child;
			$level =  $row->level;
			$parent = $row->parent;
			$name = "Lucie<3Gerrit";
			$link = 'http://www.wikidata.org/wiki/' . $qnr;

			$new_child['text'] = $qnr;
			$new_child['children'] = true;
			$new_child['id'] = $qnr;
			$a_attr_elements['href'] = $link;
			$new_child['a_attr'] = $a_attr_elements;
			array_push($children, $new_child);

		}
		echo json_encode($children);
	}
?>
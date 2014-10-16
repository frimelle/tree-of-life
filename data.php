<?php	

	session_start();
	$db = mysqli_connect( "localhost", "root", "password", "tree_db" );
	if (!$db) {
		echo mysqli_connect_error();
		return;
	}

	$post_data = array();
	$child_array = array();

	$parent = 'Q6475800';
	$qstring = "SELECT * FROM node WHERE parent='$parent'";
	$qresult = mysqli_query( $db, $qstring );
	while( $row = mysqli_fetch_object( $qresult ) ) {
		$child = $row->child;
		$child_array['level'] =  $row->level;
		$child_array['name'] = " ";
		$post_data[$child] = $child_array;
	}

	print json_encode($post_data);

?>
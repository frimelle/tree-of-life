<?php

/**
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


    $root = 'Q2382443';
    $root_name_default = 'biota';
    $root_link_default = 'http://www.wikidata.org/wiki/' . $root;


    #check if this is the first node, if it is, use the root
    if (!isset($_GET['entity_id']) || $_GET['entity_id'] == '#') {
        $root_name = get_label($root, $lang);
        
        if ($root_name == "") {
            $root_name = $root_name_default;
        } 
        $root_link = get_wiki_url($root, $lang);
        if ($root_link == "") {
            $root_link = $root_name_default;
        } 

        echo '[{"text":"' . $root_name . '","children":true, "id":"' . $root . '", "link":"' . $root_link . '"}]';
    } else {
        #get the entity_id of the node that was clicked
        $parent = $_GET['entity_id'];
        $qstring = "SELECT * FROM node WHERE parent='$parent'";
        $qresult = mysqli_query( $db, $qstring );
        $new_child = array();
        $children = array();

        while( $row = mysqli_fetch_object( $qresult ) ) {
            $entity_id = $row->child;
            //$name =  $row->name;
            $parent = $row->parent;
            $has_children = $row->hasChildren; //this is onle for Tree.py not Tree-quick.php 

            $name = get_label($entity_id, $lang);
            if ($name == "") {
                $name = $entity_id;
            }
            
            $link = get_wiki_url($entity_id, $lang);
            if ( $link == "" ) {
                $link = 'http://www.wikidata.org/wiki/' . $entity_id;
            }

            #set the name if there is a name in the database, if not set the entity_id as name
            if( $name != "" ) {
                $new_child['text'] = $name;
            } else {
                $new_child['text'] = $entity_id;
            }
            //this is onle for Tree.py not Tree-quick.php, otherwise just set $new_child['children'] = true; 
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
        #return the correct json array
        echo json_encode($children);
    }
?>
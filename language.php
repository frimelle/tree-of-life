<?php    

    session_start();
    
    if (isset($_GET['Submit'])) {
        $_SESSION['lang'] = $_GET['lang'];
        include("index.html");
    } else {
        $_SESSION['lang'] = 'en';
    }

?>
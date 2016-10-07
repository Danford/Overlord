<?php 

$baseurl = httproot.'login/' ;
$postdir = $oe_modules['login'].'api/' ;

switch( $apiCall ) {

    case "login":
        include( $postdir.'login.php' ) ;
        die() ;

    case "passwordresetrequest" :
        include( $postdir."passwordresetrequest.php" ) ;
        die() ;

    case "passwordreset":
        include( $postdir."passwordreset.php" ) ;
        die() ;
}
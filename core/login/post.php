<?php 

$baseurl = httproot.'login/' ;
$postdir = $oe_modules['login'].'post/' ;

switch( $_POST["oe_formid"] ) {

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
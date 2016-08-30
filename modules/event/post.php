<?php

if( $user->id == 0 ){ die('not logged in') ; } // THERE IS NO REASON THEY SHOULD BE HERE.

$baseurl = httproot.'event/' ;
$pagedir = $oe_modules['event']."post/" ;

switch( $_POST["oe_formid"] ) {

    case 'create':
    case 'edit':

        include $pagedir."create_or_edit.php" ;
        die();

    case 'invite':
    case 'approve_invitation':
    case 'request':
    case 'rsvp':

        include $pagedir."create_or_edit.php" ;
        die();
        
}
$post->json_reply('FAIL') ;
die( 'not routed' ) ;
<?php

if( $user->id == 0 ){ die('not logged in') ; } // THERE IS NO REASON THEY SHOULD BE HERE.

$baseurl = httproot.'profile/' ;
$pagedir = $oe_modules['profile']."api/" ;

if( $apiCall ){

    // eventually the ability to check a username will be here.
}

$user->require_login() ;

switch( $apiCall ) {

    case 'editProfile':
        
        include( $pagedir.'profile.php' ) ;
        die() ;

    case 'addFriend' :
    case 'removeFriend' :
    case 'confirmFriend' :
    case 'denyFriend' :
    case 'cancelFriendrq' :
    case 'blockUser' :

        include( $pagedir."friendmanagement.php") ;
        die();

        // API ONLY CALLS

    case 'getFriends':

        include( $pagedir."api.php" ) ;
        die();
}
$post->json_reply('FAIL') ;
die( 'FAIL' ) ;
<?php

if( $user->id == 0 ){ die('not logged in') ; } // THERE IS NO REASON THEY SHOULD BE HERE.

$baseurl = httproot.'group/' ;
$pagedir = $oe_modules['group']."post/" ;

switch( $_POST["oe_formid"] ) {
    
    case 'create':
    case 'edit':
        
        include $pagedir."profile.php" ;
        die();
        
    case 'newthread':
    case 'message':
        
        include $pagedir."threads_and_messages.php" ;
        die();
        
    case 'join':
    case 'leave':
    case 'notifications':
    case 'make_moderator':
    case 'remove_moderator':
    case 'ban_member':
    case 'unban_member':
        
        include( $pagedir."membership.php" );
        die() ;
        
    case 'invite':
    case 'request':
    case 'approve_request':
    case 'approve_invitation':
        
        include( $pagedir."invitations.php" ) ;
        die() ;
}
$post->json_reply('FAIL') ;
die( 'not routed by post.php' ) ;
<?php

$pagedir = $oe_plugins['invitations']."pages/" ;
include( $oe_plugins['invitations']."lib/invite_minion.php" ) ;

$invite = new invite_minion() ;

switch( $uri[$pos] ) {
    
    case "./final":

        $invitables = $invite->get_invitables() ;
        
        include( $pagedir."invite.php" ) ;
        die() ;
        
        
    case "moderate":
        
        $moderatables = $invite->get_moderatables() ;
        
        include( $pagedir."moderate.php" ) ;
        die() ;
    
}


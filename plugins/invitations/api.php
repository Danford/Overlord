<?php

$invite = new invite_minion() ;
include( $oe_plugins['invitations']."lib/invite_minion.php" ) ;

switch( $apiCall ){
    
    case 'inviteUsers':
        
        $r = $invite->invite_users($_POST['users'] ) ;
        $post->json_reply("SUCCESS", [ 'count' => $r ] ) ;
        $post->return_to_form() ;
    
    case 'inviteGroups':
        
        $r = $invite->invite_groups($_POST['groups'] ) ;
        $post->json_reply("SUCCESS", [ 'count' => $r ] ) ;
        $post->return_to_form() ;
        
    case 'inviteMixed':
            
        $r1 = $invite->invite_users($_POST['users'] ) ;
        $r2 = $invite->invite_groups($_POST['groups'] ) ;
        $post->json_reply("SUCCESS", [ 'count' => ( $r1 + $r2 ) ] ) ;
        $post->return_to_form() ;
    
    case 'approveInvitations':
        
        $r = $invite->approve_invites($_POST['invitees']) ;
        $post->json_reply("SUCCESS", [ 'count' => $r ] ) ;
        $post->return_to_form() ;

    case 'getInvitables':
    
        $post->json_reply("SUCCESS", $invite->get_invitables() ) ;
        die() ;
        
    case 'getModeratables':
        
        $post->json_reply("SUCCESS", $invite->get_moderatables() ) ;
        die() ;
    
    case 'uninvite':
        
        $post->json_reply("SUCCESS", $invite->uninvite( $_POST['invitees'] ) ) ;
        $post->return_to_form() ;
}

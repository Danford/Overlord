<?php

if( preg_match( '/^[0-9]*$/', $_POST['event_id'] ) != 0 ){
    $event = new event_minion( $_POST['event_id'] ) ;
} else { die( ) ; }

switch( $_POST['oe_formid'] ){

    case "invite":
        
        if( $event->group == 0 or $event->group == null ){
        
            // not a group event
        
        
            if( $event->is_organiser() or $event->type == 1 ){
        
                // create approved invitation
        
                $type = 0 ;
        
            } else {
        
                // create an invitation for a moderator to approve
        
                $type = 1 ;
            }
        
        } else {
        
            // group event
        
            include( $oe_modules['group']."lib/group_minion.php" );
        
            $group = new group_minion( $event->group ) ;
        
            if( ! $group->is_member( $user->id() ) ){ $post->json_reply('FAIL') ; }
        
            if( $group->is_organiser() or $group->type == 1 ){
        
                // create approved invitation
        
                $type = 0 ;
        
            } else {
        
                // create an invitation for a moderator to approve
        
                $type = 1 ;
            }
        
        
        }
        
        foreach( $_POST["invitees"] as $invited ){
        
            $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$event->id."', `user_id`='".$invited."', rsvp='0',
                    type='".$type."', `invited_by`='".$user->id."'" ) ;
        }
        
        $post->json_reply('SUCCESS') ;
        header( "Location: ".$_POST['oe_return'] ) ;
        die();
        

    case 'approve_invitation':

        if( $event->group == null or $event->group == 0 ){
        
        	if( ! $event->is_organiser() ){
        		$post->json_reply( 'FAIL' ) ;
        		die() ;
        	}
        
        } else {
        
        	include( $oe_modules['group']."lib/group_minion.php" ) ;
       
       		$group = new group_minion( $event->group ) ;
        
        	if( ! $group->is_moderator() ){
        		$post->json_reply( 'FAIL' ) ;
        		die() ;
        	}
        }
        
        
        verify_invitations( $_POST["invitees"] ) ;
        $db->update( "UPDATE `event_invite` SET `type`='0' WHERE `event_id`='".$event->id."'
            AND `user_id` IN (".render_array_as_string($_POST["invitees"]).")" ) ;
        $post->json_reply('SUCCESS') ;
    
    	header( "Location: ".$_POST['oe_return'] ) ;
    	die();

    case 'request':

        $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$event->id."', `user_id`='".$user->id."',
                    type='2', `invited_by`='".$user->id."'" ) ;

        $post->json_reply('SUCCESS') ;
        header( "Location: /event/".$event->id ) ;
        die();

    case 'rsvp':
        
        $event->set_rsvp( $_POST['rsvp'] ) ;
        $post->json_reply('SUCCESS') ;
        header( "Location: /event/".$event->id ) ;
        
        die() ;
        

}
$post->json_reply('FAIL') ;
die( 'not routed by module' ) ;


function verify_invitations( $list ){
    
    global $post ;
    
    foreach( $list as $item ){
        if( preg_match( '/^[0-9]*$/', $item ) == 0 ){ $post->json_reply( 'FAIL') ; die() ; }
    }

}
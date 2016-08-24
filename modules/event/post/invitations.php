<?php

if( preg_match( '/^[0-9]*$/', $_POST['event_id'] ) != 0 ){
    $event = new event_minion( $_POST['event_id'] ) ;
} else { die( ) ; }

switch( $_POST['oe_formid'] ){

    case "invite":

        verify_invitations( $_POST["invitees"] ) ;

        if( $event->is_organiser() or $event->type == 1 ){

            // create approved invitation

            foreach( $_POST["invitees"] as $invited ){

                $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$event->id."', `user_id`='".$invited."', rsvp='0', 
                    type='0', `invited_by`='".$user->id."'" ) ;
            }

        } else {

            // create an invitation for a moderator to approve

            foreach( $_POST["invitees"] as $invited ){

                $db->insert( "INSERT INTO `group_invite` SET `group_id`='".$group->id."', `user_id`='".$invited."', rsvp='0',
                    type='1', `invited_by`='".$user->id."'" ) ;
            }
        }

        header( "Location: ".$_POST['oe_return'] ) ;
        die();

    case 'approve_invitation':

        if( $group->is_moderator() ){
            verify_invitations( $_POST["invitees"] ) ;
            $db->update( "UPDATE `event_invite` SET `type`='0' WHERE `group_id`='".$group->id."'
                AND `user_id` IN (".render_array_as_string($_POST["invitees"]).")" ) ;
        }

        header( "Location: ".$_POST['oe_return'] ) ;
        die();

    case 'request':

        $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$event->id."', `user_id`='".$user->id."',
                    type='2', `invited_by`='".$user->id."'" ) ;

        header( "Location: /event/".$event->id ) ;
        die();

    case 'rsvp':
        
        $event->set_rsvp( $_POST['rsvp'] ) ;

        header( "Location: /event/".$event->id ) ;
        
        die() ;
        

}

die( 'not routed by module' ) ;


function verify_invitations( $list ){

    foreach( $list as $item ){
        if( preg_match( '/^[0-9]*$/', $item ) == 0 ){ die() ; }
    }

}
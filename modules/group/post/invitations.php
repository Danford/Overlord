<?php


if( preg_match( '/^[0-9]*$/', $_POST['group_id'] ) != 0 ){
    $group = new group_minion( $_POST['group_id'] ) ;
} else { $post->json_reply('FAIL') ; die( ) ; }

switch( $_POST['oe_formid'] ){
    
    case "invite":
        
        verify_invitations( $_POST["invitees"] ) ;
        
        if( $group->is_moderator() or $group->type == 1 ){
            
            // create approved invitation
            
            foreach( $_POST["invitees"] as $invited ){
                
                $db->insert( "INSERT INTO `group_invite` SET `group_id`='".$group->id."', `user_id`='".$db-sanitize( $invited )."',
                    type='0', `invited_by`='".$user->id."'" ) ;                
            }
            
        } else {
            
            // create an invitation for a moderator to approve
            
            foreach( $_POST["invitees"] as $invited ){
                
                $db->insert( "INSERT INTO `group_invite` SET `group_id`='".$group->id."', `user_id`='".$invited."',
                    type='1', `invited_by`='".$user->id."'" ) ;                
            }            
        }
        
        $post->json_reply('SUCCESS') ;
        
        header( "Location: ".$_POST['oe_return'] ) ;
        die();

    case 'approve_invitation':        
        
        if( $group->is_moderator() ){
            verify_invitations( $_POST["invitees"] ) ;
            $db->update( "UPDATE `group_invite` SET `type`='0' WHERE `group_id`='".$group->id."' 
                AND `user_id` IN (".render_array_as_string($_POST["invitees"]).")" ) ;
            $post->json_reply('SUCCESS') ;
        }
        
        $post->json_reply( 'ERROR', 'unauthorised' ) ;
        
        header( "Location: ".$_POST['oe_return'] ) ;
        die();
        
    case 'request':
        
        if( $group->is_member($user->id )) {
            
            $db->insert( "INSERT INTO `group_invite` SET `group_id`='".$group->id."', `user_id`='".$user->id."',
                        type='2', `invited_by`='".$user->id."'" ) ;
    
            $post->json_reply('SUCCESS') ;
        }
        
        $post->json_reply('ERROR', 'unauthorised') ;
        
        header( "Location: /group/".$group->id ) ;
        die();
        
    case 'approve_request':
        
        if( $group->is_moderator() ){
            
            verify_invitations($_POST['invitees'] ) ;
            
            foreach( $_POST['invitees'] as $member ) {
                
                $db->insert( "INSERT INTO `group_members` SET `group_id`='".$group->id."', `member_id`='".$member."',
                                `timestamp`='".oe_time()."'" ) ; 
                $db->update( "DELETE FROM `group_invite` WHERE `group_id`='".$group->id."' AND `user_id`='".$member."'") ;
                
            }
            $post->json_reply('SUCCESS') ;
            header( "Location: /group/".$group->id ) ;
            
        }
        $post->json_reply('ERROR', 'unauthorised') ;
        die();
        
    
}
$post->json_reply('FAIL') ;
die( 'not routed by module' ) ;


function verify_invitations( $list ){
    
    global $post ;
    
    foreach( $list as $item ){
        if( preg_match( '/^[0-9]*$/', $item ) == 0 ){ $post->json_reply('FAIL') ; die() ; }
    }
    
}
<?php

if( preg_match( '/^[0-9]*$/', $_POST['group_id'] ) != 0 ){
    $group = new group_minion( $_POST['group_id'] ) ;
} else { die() ; }

switch( $_POST['oe_formid'] ) {

    case 'join':
        
        if( $group->type == 1 or $group->is_invited() ){
            
            // you can't join a closed or secret group unless you've been invited
            
            $db->insert( "INSERT INTO `group_members` SET `member_id`='".$user->id."', `group_id`='".$_POST['group_id']."', `timestamp`='".oe_time()."'" ) ;
            $db->update( "DELETE FROM `group_invite` WHERE `group_id`='".$group->id."' AND `user_id`='".$user->id."'" ) ;
            
            header( "Location: /group/".$_POST['group_id']."/notifications" );
            
        } 
        die(); 
        
    case 'leave':
        
        $db->update( "DELETE FROM `group_members` WHERE `group_id`='".$group->id."' AND `member_id`='".$user->id."'" ) ;
            
            header( "Location: /group/".$_POST['group_id'] ) ;
            die() ;
        
    case 'notifications':
        
        if( ( $_POST['notify_message'] != '1' and $_POST['notify_message'] != '0' ) or 
            ( $_POST['notify_thread'] != '1' and $_POST['notify_thread'] != '0' ) ) {
                die() ;
            }
        
        $db->update( "UPDATE `group_members` SET `notify_message`='".$_POST['notify_message']."', `notify_thread`='".$_POST['notify_thread']."'
                WHERE `group_id`='".$_POST['group_id']."' and `member_id`='".$user->id."'" );
        
        header( "Location: /group/".$_POST['group_id'] ) ;
        die() ;
        
    case 'make_moderator':
        
        if( $group->is_owner() ){
            
            verify_number($_POST['member'] ) ;
            
            $db->insert( "INSERT INTO `group_moderator` SET `group_id`='".$group->id."', `mod_id`='".$_POST['member']."'" ) ;
            
            header( 'Location: '.$_POST['oe_return'] ) ;
        }  
        
        die() ;
        
    case 'remove_moderator':
        
        if( $group->is_owner() ){
            
            verify_number($_POST['member'] ) ;
            
            $db->update( "DELETE FROM `group_moderator` WHERE `group_id`='".$group->id."' AND `mod_id`='".$_POST['member']."'" ) ;
            
            header( 'Location: '.$_POST['oe_return'] ) ;
        }
        
        die() ;
        
    case 'ban_member':
        
        if( $group->is_moderator() ){
            
            verify_number($_POST['member'] ) ;
            
            $db->insert( "INSERT INTO `group_block` SET `group_id`='".$group->id."', `blocked_user`='".$_POST['member']."', `blocked_by`='".$user->id."'" ) ;
            
            $db->update( "DELETE FROM `group_members` where `member_id`='".$_POST['member']."'" ) ;
            
            header( 'Location: '.$_POST['oe_return'] ) ;
            
        }
        die() ;
        
    case 'unban_member':
        
        if( $group->is_moderator() ){
            
            verify_number($_POST['member'] ) ;
            
            $db->insert( "DELETE FROM `group_block` WHERE `group_id`='".$group->id."' AND `blocked_user`='".$_POST['member']."'" ) ;
            
            header( 'Location: '.$_POST['oe_return'] ) ;
            
        }
        die() ;
}
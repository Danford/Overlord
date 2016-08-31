<?php
switch( $_POST['oe_formid'] ) {
    
    case 'newthread':
        
        if( ! isset( $_POST['group_id'] ) or preg_match(  '/^[0-9]*$/', $_POST['group_id'] ) == 0 ){
            $post->json_reply('FAIL') ;
            die();
        }
        
        $group = new group_minion( $_POST['group_id'] ) ;
        
        if( $group->access != 'full' ) { $post->json_reply( 'FAIL' ) ; die() ; }
        
        $post->hold( 'subject', 'message' ) ;

        if( $group->is_moderator() ){
            $post->hold( 'sticky' ) ;
        } else {
            $_POST['sticky'] = '0' ;
        }
        
        $post->require_true( $_POST['subject'] != '', 'subject', 'Subject cannot be blank.' ) ;
        $post->require_true( strlen($_POST['subject']) < 101, 'subject', 'Subject cannot be more than 100 characters.' ) ;
        $post->require_true( $_POST['message'] != '', 'message', 'Message cannot be blank.' ) ;
        
        $post->checkpoint() ;

        $_POST['group_id'] = $group->id ;
        $_POST['user_id'] = $user->id ;
        $_POST['timestamp'] = oe_time() ;
        $_POST['latest_timestamp'] =  $_POST['timestamp'] ;
        
        $s = $db->build_set_string_from_post( 'group_id', 'user_id', 'subject', 'sticky', 'latest_timestamp' ) ;
                
        $thread_id = $db->insert( "INSERT INTO `group_thread` SET ".$s );
        
        $db->insert( "INSERT INTO `group_message` SET `thread_id`='".$thread_id."', ".$db->build_set_string_from_post('user_id', 'message', 'timestamp' ) );
        
        $post->json_reply('SUCCESS', $thread_id ) ;
        
        header( 'Location: /group/'.$group->id.'/thread/'.$thread_id ) ;
        die() ;
        
    break;
    
    case "message":
        
        $post->hold("message") ;
        
        $post->require_true( $_POST["message"] != '', 'message', 'Message is blank' ) ;
        $post->checkpoint ;
        
        $g = $db->get_field( "SELECT `group_id` FROM `group_thread` WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'" ) ;
        
        if( $g == false ){
            $post->json_reply('FAIL' ) ;
            die() ;
        } else {
            $group = new group_minion($g) ;
        }
        
        if( $group->access != 'full' ) { $post->json_reply( 'FAIL' ) ; die(); }
        
        $_POST["timestamp"] = oe_time() ;
        $_POST['user_id'] = $user->id ;
        
        $db->insert( "INSERT INTO `group_message` SET ".$db->build_set_string_from_post( 'user_id', 'timestamp', 'message', 'thread_id' ));
        $db->update( "UPDATE `group_thread` SET `latest_timestamp`='".$_POST['timestamp']."' WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'") ;
        
        // determine to which page to send them
        
        $c = $db->get_field( "SELECT COUNT(*) FROM `group_message` where `thread_id`='".$_POST['thread_id']."'" ) ;
        
        if( $c <= messages_per_page ) {
            // go to first page
            $post->json_reply( 'SUCCESS', 1 ) ;
            header( 'Location: /group/'.$group->id."/thread/".$_POST['thread_id'] ) ;
            die();
        }
        
        if( $c % messages_per_page == 0 ) {
            $page = ( $c / messages_per_page )  ;
        } else {
            $page = ( ( $c - ( $c % messages_per_page )) / messages_per_page ) + 1 ; 
        }

        $post->json_reply('SUCCESS', $page ) ;
        header( 'Location: /group/'.$group->id."/thread/".$_POST['thread_id'].'/page/'.$page ) ;
        die();
        
    break ;
    
    case "makesticky":
    case "makeunsticky":
        
        $g = $db->get_field( "SELECT `group_id`, `group` FROM `group_thread` WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'" ) ;
        
        if( $g == false ){
            $post->json_reply('FAIL') ;
            die() ;
        } else {
            $group = new group_minion($g['group_id']) ;
        }
        
        if( ! $group->is_moderator() ) { $post->json_reply('FAIL') ; die(); }
        
        if( $_POST['oe_formid'] == "makesticky" ) {
            $sticky = 1 ;
        } else {
            $sticky = 0 ;
        }
        
        $db->update( "UPDATE `group_thread` SET `sticky`='".$sticky."' WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'" ) ;
        
        $post->json_reply('SUCCESS') ;
        header( "Location: /group/".$g['group_id']."/threads" ) ;
        die();
        
    break ;

    case "deletethread":
        
        $g = $db->get_assoc( "SELECT `group_id`, `user_id` FROM `group_thread` WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'" ) ;
        
        if( $g == false ){
            $post->json_reply('FAIL') ;
            die() ;
        } else {
            $group = new group_minion($g['group_id']) ;
        }
        
        if( ! $group->is_moderator() ) { $post->json_reply('ERROR', 'unauthorised' ) ; die(); }
        
        $db->update( "DELETE FROM `group_thread`, `group_message` WHERE `thread_id`='".$db->sanitize( $_POST['thread_id'] )."'" ) ;
        $post->json_reply('SUCCESS') ;
        header( "Location: /group/".$g['group_id']."/threads" );
        die() ;
        
    break ;
    
    case "deletemessage":
    
        $g = $db->get_assoc( "SELECT `group_message`.`user_id`, `group_thread`.`group_id` FROM `group_message`, `group_thread` WHERE
                                `group_message`.`message_id`='".$db->sanitize( $_POST['message_id'] )."' AND
                                `group_message`.`thread_id` = `group_thread`.`thread_id`" );
        
        if( $g == false ){
            $post->json_reply('FAIL') ;
            die() ;
        } else {
            $group = new group_minion($g['group_id']) ;
        }
        
        if( ! $group->is_moderator() ) { $post->json_reply('ERROR', 'unauthorised' ) ; die(); }
        
        $db->update( "DELETE FROM `group_message` WHERE `message_id`='".$db->sanitize( $_POST['message_id'] )."'" ) ;
        
        $post->json_reply('SUCCESS') ;
        header( "Location: /group/".$g['group_id']."/threads" );
        die() ;
        
    break ;
}
<?php

include( $oe_modules['profile']."lib/album.lib.php" ) ;

switch( $_POST['oe_formid'] ){
    
    case 'new_writing':
        
        $post->hold( 'private', 'title', 'subtitle', 'content', 'setavatar', 'album', 'new_album_title', 'new_album_description' );

        $post->require_true( $_POST['title'] != '' , 'title', 'Title is a required field.' ) ;
        $post->require_true( strlen( $_POST['title']) < 76 , 'title', 'Title must be 75 characters or less.' ) ;
        $post->require_true( strlen( $_POST['description']) < 256, 'description', 'Description must be 75 characters or less.' ) ;
        $post->require_true( strlen( $_POST['content']) > 8, 'content', 'You need to actually write something.' ) ;
        
        $post->checkpoint() ;

        if( $_POST["album"] == "New" ){
            
            $post->require_true( $_POST['new_album_title'] != '', 'new_album_title', 'Albums must have titles.' ) ;
            $post->checkpoint() ;
        
            $o['album'] = create_album($_POST['new_album_title'], $_POST['new_album_description'], 'prose', ( $_POST['private'] == 1 ) ) ;
        
        } elseif ( $_POST["album"] != "None" ) {
        
            increment_album($_POST['album'], 'prose', ($_POST['private'] == 1 ) ) ;
        
            $o['album'] = $_POST['album'] ;
        
        } 
        
        increment_profile_item_count( 'prose', $_POST['private'] == 1 ) ;
        

        $o['timestamp'] = oe_time() ;
        $o['owner'] = $user->id ;
        
        $s = $db->build_set_string_from_post( 'title', 'subtitle', 'content', 'private' ) ;

        $proseid = $db->insert( 'INSERT INTO `profile_prose` SET '.$s.', '.$db->build_set_string_from_array( $o ) ) ;
        
        log_activity( 9, $proseid ) ;
        
        $post->json_reply( 'SUCCESS' ) ;
       
        header( 'Location: /profile/'.$user->id.'/writing/'.$proseid ) ;
        die() ;
        
    case 'edit_writing':

        $post->hold( 'private', 'title', 'subtitle', 'content', 'setavatar', 'album', 'new_album_title', 'new_album_description' );
        
        $post->require_true( $_POST['title'] != '' , '', 'Title is a required field.' ) ;
        $post->require_true( strlen( $_POST['title']) < 76 , 'title', 'Title must be 75 characters or less.' ) ;
        $post->require_true( strlen( $_POST['description']) < 256, 'description', 'Description must be 75 characters or less.' ) ;
        $post->require_true( strlen( $_POST['content']) > 8, 'description', 'You need to actually write something.' ) ;
        
        $post->checkpoint() ;
        
        if( $_POST['album'] == "New" ){
            
            $post->require_true( $_POST['new_album_title'] != '', 'new_album_title', 'Albums must have titles.' ) ;
            $post->checkpoint() ;
        }
        
        // album management        
        if( preg_match( '/^[0-9]*$/', $_POST['prose_id']) == 0 ) { die('invalid'); } // invalid prose id
        
        
        $oldinfo = $db->get_assoc( "SELECT `private`, `album`
                                FROM `profile_prose` WHERE `prose_id`='".$_POST['prose_id']."' AND `owner`='".$user->id."'" ) ;
        
         if( $oldinfo == false ){ die(); } // writing doesn't exist or isn't theirs
       
        if( $_POST["album"] == "None" ){ $_POST['album'] = '' ; } 
        
        // deal with album counts
        
        if( $oldinfo['album'] != $_POST['album'] ){
            
            // album change
            
            if( $oldinfo['album'] != '' ){
                decrement_album($oldinfo['album'], 'prose', ( $oldinfo['private'] == 1 ) ) ;
            }
            
            if( $_POST['album'] == "New" ) {
                $_POST['album'] = create_album($_POST['new_album_title'], $_POST['new_album_description'], 'prose', ( $_POST['private'] == 1 ) ) ;
            } elseif( $_POST['album'] != '' ){
                increment_album($_POST['album'], 'prose', ( $_POST['private'] == 1 ) ) ;                
            }
            
        }
        
        if( $oldinfo['private'] != $_POST['private'] ){
        
        // deal with profile counts
            decrement_profile_item_count('prose', ( $oldinfo['private'] == 1 ) ) ;
            increment_profile_item_count('prose', ( $_POST['private'] == 1 ) ) ;
        }
        
        $db->update( "UPDATE `profile_prose` SET ".$db->build_set_string_from_post( 'title', 'subtitle', 'content', 'album')." WHERE 
            `prose_id`='".$_POST['prose_id']."'" ) ;
        
        $post->json_reply( 'SUCCESS' ) ;
        
        header( 'Location: /profile/'.$user->id.'/writing/'.$_POST['prose_id'] ) ;
        die() ;
        
    case 'delete_writing':

        if( preg_match( '/^[0-9]*$/', $_POST['prose_id']) == 0 ) { die(); } // invalid prose id
        
        $detail = $db->get_assoc( "SELECT `album`, `private` FROM `profile_prose` WHERE `prose_id`='".$_POST['prose_id']."' AND `owner`='".$user->id."'" ) ;
        
        if( $detail == false ){ die() ; }
        
        $db->update( "DELETE FROM `profile_prose` WHERE `prose_id`='".$_POST['prose_id']."'" ) ;
            
        if( $detail['album'] != '' ) {
            decrement_album( $detail['album'], 'prose', ( $detail['private'] == 1 ) ) ; 
        }
        
        decrement_profile_item_count('prose', ( $detail['private'] == 1 ) ) ;
        
        $post->json_reply( 'SUCCESS' ) ;
        
        header( "Location: /" );
        die() ;
        
        
        
}
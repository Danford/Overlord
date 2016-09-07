<?php



switch( $_POST["oe_formid"] ) {

    case 'deletecomment':
    case 'deleteComment':
        
        if( ( preg_match( '/^[0-9]*$/', $_POST['comment_id']) == 0 )
                or 
            ( $_POST['type'] != 'photo' and $_POST['type'] != 'video' and $_POST['type'] != 'prose' ) ){
            
            $post->json_reply( 'FAIL' ) ;
            die() ;
        }
        
        $a = $db->get_assoc( "SELECT `profile_".$db->sanitize( $_POST['type'] )."_comment`.`user_id`, `profile_".$db->sanitize( $_POST['type'] )."`.`owner`,
            `profile_".$db->sanitize( $_POST['type'] )."_comment`.`".$db->sanitize( $_POST['type'] )."_id` as `id`
            FROM `profile_".$db->sanitize( $_POST['type'] )."_comment`, `profile_".$db->sanitize( $_POST['type'] )."`
            WHERE `comment_id` = '".$db->sanitize($_POST['comment_id'])."' 
            AND `profile_".$db->sanitize( $_POST['type'] )."_comment`.`".$db->sanitize( $_POST['type'] )."_id` = `profile_".$db->sanitize( $_POST['type'] )."`.`".$db->sanitize( $_POST['type'] )."_id`" ) ;
        
        if( $user->id == $a['owner'] or $user->id == $a['user_id'] ) {
            
            // only the photo owner or the poster can delete a comment

            $db->update( "DELETE FROM `profile_".$db->sanitize( $_POST['type'] )."_comment` WHERE `comment_id`='".$db->sanitize($_POST['comment_id'])."'" );
            $db->update( "DELETE FROM `user_notifications` WHERE `ref`='".$db->sanitize($_POST['comment_id'])."'" );
            $db->update( "DELETE FROM `user_activity` WHERE `ref`='".$db->sanitize($_POST['comment_id'])."'" );
            
            $db->update( "UPDATE `profile_".$db->sanitize( $_POST['type'] )."` SET `comments` = `comments` - 1 
                            WHERE `".$db->sanitize( $_POST['type'] )."_id`='".$a['id']."'" );


            $post->json_reply( 'SUCCESS' ) ;   
            
            $origin = explode( '#', $_POST['oe_return'] ) ;
            header( 'Location: '.$origin[0] ) ;
            
        }
        
        $post->json_reply( 'ERROR', 'unauthorised' ) ;
        
        die() ;
        
    case 'addcomment':
    case 'addComment':
        
        // any failure here will be the result of mucking with the form submission
        // so there will be no errors, just death()
        

        if( ( preg_match( '/^[0-9]*$/', $db->sanitize( $_POST['id'] )) == 0 )
                or 
            ( $_POST['type'] != 'photo' and $_POST['type'] != 'video' and $_POST['type'] != 'prose' ) ){
            
                $post->json_reply( 'FAIL' ) ;
                die() ;
        }
        
        $q = "SELECT `owner` FROM `profile_".$db->sanitize( $_POST['type'] )."` WHERE `".$db->sanitize( $_POST['type'] )."_id`='".$db->sanitize( $_POST['id'] )."'" ;
        
        $owner = $db->get_field( $q ) ;

        if( $user->is_blocked( $owner ) ){
            $post->json_reply( 'ERROR', 'unauthorised' ) ;
            die() ;
        }
        
        $q = "INSERT INTO `profile_".$db->sanitize( $_POST['type'] )."_comment` SET `timestamp`='".oe_time()."', 
            `".$db->sanitize( $_POST['type'] )."_id`='".$db->sanitize( $_POST['id'] )."', `user_id`='".$user->id."',
            `comment`='".$db->sanitize($_POST['comment'])."'" ;

        $id = $db->insert( $q ) ;
        
        $q = "update `profile_".$db->sanitize( $_POST['type'] )."` SET `comments` = `comments` + 1 WHERE `".$db->sanitize( $_POST['type'] )."_id` = '".$db->sanitize( $_POST['id'] )."'" ;
        
        
        $db->update( $q ) ;

        $notify['photo'] = 4 ;
        $notify['prose'] = 6 ;
        $notify['video'] = 8 ;
        $activity['photo'] = 6 ;
        $activity['prose'] = 10 ;
        $activity['video'] = 14 ;
        
        notify_user( $owner, $notify[$_POST['type']], $id ) ;
        log_activity( $activity[$_POST['type']], $id ) ;
        
        $post->json_reply( 'SUCCESS', [ 'comment_id' => $id ] ) ;
        
        $origin = explode( '#', $_POST['oe_return'] ) ;
        
        header( 'Location: '.$origin[0]."#".$id ) ;
        die() ;

    case 'like':
        
        if( ( preg_match( '/^[0-9]*$/', $db->sanitize( $_POST['id'] )) == 0 )
            or
            ( $_POST['type'] != 'photo' and $_POST['type'] != 'video' and $_POST['type'] != 'prose' ) ){
                
                
                $post->json_reply( 'FAIL' ) ;
                die() ;
        }
        
        $q = "SELECT `owner` FROM `profile_".$db->sanitize( $_POST['type'] )."` WHERE `".$db->sanitize( $_POST['type'] )."_id`='".$db->sanitize( $_POST['id'] )."'" ;
        
        $owner = $db->get_field( $q ) ;
        
        if( $user->is_blocked( $owner ) ){
            $post->json_reply( 'ERROR', 'unauthorised' ) ;
            die() ;
        }        
        
        $like_id = $db->insert( "INSERT INTO `profile_".$db->sanitize( $_POST['type'] )."_like` 
            SET `".$db->sanitize( $_POST['type'] )."_id`='".$db->sanitize( $_POST['id'] )."', `liked_by`='".$user->id."', `timestamp`='".oe_time()."'" );
        
        $q = "update `profile_".$db->sanitize( $_POST['type'] )."` SET `likes` = `likes` + 1 WHERE `".$db->sanitize( $_POST['type'] )."_id` = '".$db->sanitize( $_POST['id'] )."'" ;

        $db->update( $q ) ;

        $notify['photo'] = 5 ;
        $notify['prose'] = 7 ;
        $notify['video'] = 9 ;
        $activity['photo'] = 7 ;
        $activity['prose'] = 11 ;
        $activity['video'] = 15 ;
        
        notify_user( $owner, $notify[$_POST['type']], $id ) ;
        log_activity( $activity[$_POST['type']], $id ) ;
        
        $post->json_reply( 'SUCCESS' ) ;
                
        header( "Location: ".$_POST['oe_return'] ) ;
        die() ;

    case 'unlike':
    case 'unLike':
        
        if( ( preg_match( '/^[0-9]*$/', $db->sanitize( $_POST['id'] )) == 0 )
            or
            ( $_POST['type'] != 'photo' and $_POST['type'] != 'video' and $_POST['type'] != 'prose' ) ){
                
                $post->json_reply( 'FAIL' ) ;
                die() ;
        }
        
        $ref = $db->get_field( "SELECT `id` FROM `profile_".$db->sanitize( $_POST['type'] )."_like` WHERE `".$db->sanitize( $_POST['type'] )."_id`='".$db->sanitize( $_POST['id'] )."' AND `liked_by`='".$user->id."'" );
        
        if( $ref != false ){
            $db->update( "update `profile_".$db->sanitize( $_POST['type'] )."` SET `likes` = `likes` - 1 WHERE `".$db->sanitize( $_POST['type'] )."_id` = '".$db->sanitize( $_POST['id'] )."'" ) ;
            $db->update( "DELETE FROM `profile_".$db->sanitize( $_POST['type'] )."_like` WHERE `id`='".$ref."'" ) ;
            $db->update( "DELETE FROM `user_notifications` WHERE `ref`='".$ref."'" ) ;
            $db->update( "DELETE FROM `user_activity` WHERE `ref`='".$ref."'" ) ;
        
            if( $post->is_a_json_request ){
                $post->json_reply( 'SUCCESS' ) ;
            }
        }

        $post->json_reply( 'ERROR', 'not found' ) ;
        
        header( "Location: ".$_POST['oe_return'] ) ;
        die() ;
}

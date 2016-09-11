<?php

include( $oe_modules['profile']."lib/album.lib.php" ) ;

switch( $_POST['oe_formid'] ){
    
    case "imageUpload":
        
        
        if( $_POST['album'] == "New" ){
            
            $post->require_true( $_POST['new_album_title'] != '', 'new_album_title', 'Albums must have titles.' ) ;
            $post->checkpoint() ;
        }
    
        $filekey = createGUID() ;
    
        include ( oe_lib.'imagetools.php' ) ;
    
        // resize and convert if necessary, copy to ul_img_dir
    
        if( $imageFileType == "png" or $imageFileType == "PNG" ) {
    
            resize_png( $_FILES["photo"]["tmp_name"], ul_img_dir."user.".$user->id.".".$filekey.".png" , max_image_width, max_image_height ) ;
    
        } else {
    
            convert_and_size_jpg( $_FILES["photo"]["tmp_name"], ul_img_dir."user.".$user->id.".".$filekey.".png" , max_image_width, max_image_height ) ;
    
        }
    
        // create thumbnail
    
        create_square_thumb( ul_img_dir."user.".$user->id.".".$filekey.".png", ul_img_dir."user.".$user->id.".".$filekey.".thumb.png", thumbnail_size );
    
        // delete the original
    
        unlink( $_FILES["photo"]["tmp_name"] ) ;
    
        $s = $db->build_set_string_from_post( 'title', 'description', 'private' ) ;
    
        $o['file_key'] = $filekey ;
        $o['owner'] = $user->id ;
        $o['timestamp'] = oe_time() ; 
    
        if( $_POST["album"] == "New" ){
    
            $o['album'] = create_album($_POST['new_album_title'], $_POST['new_album_description'], 'photo', ( $_POST['private'] == 1 ) ) ;
    
        } elseif ( $_POST["album"] != "None" ) {
    
            increment_album($_POST['album'], 'photo', ($_POST['private'] == 1 ) ) ;
    
            $o['album'] = $_POST['album'] ;
    
        }
        
        increment_profile_item_count('photo', ( $_POST['private'] == 1 ) ) ;    
        
    
        $photoid = $db->insert( "INSERT INTO `profile_photo` SET ".$s.", ".$db->build_set_string_from_array($o) ) ;
    
        if( $_POST['setavatar'] == "on" and $_POST['private'] == "0" ){
            
            $query = "SELECT `profile_photo`.`file_key` FROM `user_profile`, `profile_photo` WHERE
                  `user_profile`.`user_id` = '".$user->id."' AND
                  `user_profile`.`avatar` = `profile_photo`.`photo_id`" ;
    
            if( ( $oldkey = $db->get_field($query) ) != false ) {
    
                //delete old profile image
    
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profile.png" ) ;
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profilethumb.png" ) ;
            }
    
            // create new profile image
    
            resize_png( ul_img_dir."user.".$user->id.".".$filekey.".png", ul_img_dir."user.".$user->id.".".$filekey.".profile.png", profile_image_size, profile_image_size );
            create_square_thumb(ul_img_dir."user.".$user->id.".".$filekey.".png", ul_img_dir."user.".$user->id.".".$filekey.".profilethumb.png", profile_thumb_size );
            $db->update( "UPDATE `user_profile` SET `avatar`='".$photoid."' WHERE `user_id` = '".$user->id."'" ) ;
            $user->avatar = $photoid ;
            $type = '28' ;
    
        } else {
    
            $type = '5' ;
        }
    
        log_activity( $type, $photoid ) ;
    
        $post->json_reply( 'SUCCESS', [ 'id' => $photoid ] ) ;
        
        header( "Location: ".$baseurl.$user->id."/photo/".$photoid ) ;
        die() ;
    
    case "editphoto":
    case "editPhoto":
        
        // actually, we're just editing the info connected to the photo.  This ain't Photoshop: The Website
        
        $post->checkbox('setavatar') ;
        $post->hold( "private", "title", "description", "setavatar", "album", "new_album_title", "new_album_description" ) ;

        $post->require_true( strlen( $_POST['title']) < 76 , 'title', 'Title must be 75 characters or less.' ) ;
        $post->require_true( strlen( $_POST['description']) < 256, 'description', 'Description must be 75 characters or less.' ) ;
        
        $post->checkpoint() ;
        
        if( $_POST['album'] == "New" ){
            
            $post->require_true( $_POST['new_album_title'] != '', 'new_album_title', 'Albums must have titles.' ) ;
            $post->checkpoint() ;
        }
        
        if( ! verify_number( $_POST['photo_id'] ) ) { $post->json_reply('FAIL'); die(); } // invalid photo id
        
        
        $oldinfo = $db->get_assoc( "SELECT `private`, `title`, `description`, `album`, `file_key`
                                FROM `profile_photo` WHERE `photo_id`='".$_POST['photo_id']."' AND `owner`='".$user->id."'" ) ;
        
         if( $oldinfo == false ){ $post->json_reply('FAIL'); die(); } // photo doesn't exist or isn't theirs
       
        if( $_POST["album"] == "None" ){ $_POST['album'] = '' ; } 
        
        // deal with album counts
        
        if( $oldinfo['album'] != $_POST['album'] ){
            
            // album change
            
            if( $oldinfo['album'] != '' ){
                decrement_album($oldinfo['album'], 'photo', ( $oldinfo['private'] == 1 ) ) ;
            }
            
            if( $_POST['album'] == "New" ) {
                $_POST['album'] = create_album($_POST['new_album_title'], $_POST['new_album_description'], 'photo', ( $_POST['private'] == 1 ) ) ;
            } elseif( $_POST['album'] != '' ){
                increment_album($_POST['album'], 'photo', ( $_POST['private'] == 1 ) ) ;                
            } 
            
        }
        
        if( $oldinfo['private'] != $_POST['private'] ){
        
        // deal with profile counts
            decrement_profile_item_count('photo', ( $oldinfo['private'] == 1 ) ) ;
            increment_profile_item_count('photo', ( $_POST['private'] == 1 ) ) ;
        }
        
        // deal with avatar
        
        if( $_POST['setavatar'] == 'on' and $_POST['photo_id'] != $user->avatar and $_POST['private'] == "0" ){
            
            //remove old avatar files, create new one
            
            $query = "SELECT `profile_photo`.`file_key` FROM `user_profile`, `profile_photo` WHERE
                  `user_profile`.`user_id` = '".$user->id."' AND
                  `user_profile`.`avatar` = `profile_photo`.`photo_id`" ;
            
            if( ( $oldkey = $db->get_field($query) ) != false ) {
            
                //delete old profile image
            
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profile.png" ) ;
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profilethumb.png" ) ;
            }
            
            // create new profile image
            
            $filekey = $db->get_field( "SELECT `file_key` FROM `profile_photo` WHERE `photo_id`='".$user->avatar."'" ) ;
            
            resize_png( ul_img_dir."user.".$user->id.".".$filekey.".png", ul_img_dir."user.".$user->id.".".$filekey.".profile.png", profile_image_size, profile_image_size );
            create_square_thumb(ul_img_dir."user.".$user->id.".".$filekey.".png", ul_img_dir."user.".$user->id.".".$filekey.".profilethumb.png", profile_thumb_size );
            
            $db->update( "UPDATE `user_profile` SET `avatar`='".$_POST['photo_id']."' WHERE `user_id`='".$user->id."'" ) ;
            $user->avatar = $_POST['photo_id'] ;
            log_activity(28, $_POST['photo_id'] ) ;
            
        } elseif( $_POST['setavatar'] == 'off' and $_POST['photo_id'] != $user->avatar ){
            
            $user->avatar = '' ;
            $db->update( "UPDATE `user_profile` SET `avatar`='' WHERE `user_id`='".$user->id."'" ) ;
            // remove current avatar
            
            $query = "SELECT `profile_photo`.`file_key` FROM `user_profile`, `profile_photo` WHERE
                  `user_profile`.`user_id` = '".$user->id."' AND
                  `user_profile`.`avatar` = `profile_photo`.`photo_id`" ;
            
            if( ( $oldkey = $db->get_field($query) ) != false ) {
            
                //delete old profile image
            
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profile.png" ) ;
                unlink( ul_img_dir."user.".$user->id.".".$oldkey.".profilethumb.png" ) ;
            }
        }
        
        // now that mess is out of the way, it's just updating the info
        
        $db->update( "UPDATE `profile_photo` SET ".$db->build_set_string_from_post( 'title', 'description', 'private', 'album' )."
                        WHERE `photo_id` = '".$_POST['photo_id']."'" );
        
        $post->json_reply( 'SUCCESS' ) ;
        
        header( 'Location: '.$baseurl.$user->id.'/photo/'.$_POST['photo_id'] ) ;
        die();
        
    case 'deletephoto':

        if( ! verify_number( $_POST['photo_id'] ) ) {$post->json_reply('FAIL'); die(); } // invalid photo id
        
        $detail = $db->get_assoc( "SELECT `file_key`, `album`, `private` FROM `profile_photo` WHERE `photo_id` = '".$_POST['photo_id']."' AND `owner`='".$user->id."'" ) ;

        if( $detail == false ){ $post->json_reply('FAIL'); die() ; }
        
        unlink( ul_img_dir."user.".$user->id.".".$detail['file_key'].".png" ) ;
        unlink( ul_img_dir."user.".$user->id.".".$detail['file_key'].".thumb.png" ) ; 

        $db->update( "DELETE FROM `profile_photo` WHERE `owner`='".$user->id."' AND `photo_id`='".$_POST['photo_id']."'" ) ;
        $db->update( "DELETE FROM `profile_photo_comment` WHERE `photo_id`='".$_POST['photo_id']."'" ) ;
        $db->update( "DELETE FROM `profile_photo_like` WHERE `photo_id`='".$_POST['photo_id']."'" ) ;
        
        if( $user->avatar == $_POST['photo_id'] ) {
 
            $db->update( "UPDATE `user_profile` SET `avatar`='' WHERE `user_id`='".$user->id."'" ) ;
            unlink( ul_img_dir."user.".$user->id.".".$detail['file_key'].".profile.png" ) ;
            unlink( ul_img_dir."user.".$user->id.".".$detail['file_key'].".profilethumb.png" ) ;
            $user->avatar = '' ; 
        } 
        
        if( $detail['album'] != '' ) {
            decrement_album($detail['album'], 'photo', ( $detail['private'] == 1 ) ) ; 
        }
        
        decrement_profile_item_count('photo', ( $detail['private'] == 1 ) ) ;
        
        $post->json_reply( 'SUCCESS' ) ;

        header( "Location: ".$baseurl.$user->id."/photos" );
        die() ;
}















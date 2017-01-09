<?php

/*
 *  since much of edit and upload are the same, they are handled here together.
 * 
 */



include( $oe_plugins['photo']."lib/photo.lib.php" ) ;

$post->checkbox('parentavatar') ;
$post->checkbox('setalbumavatar') ;

$post->hold( "privacy", "title", "description", "setparentavatar", "setalbumvatar", "album", 
             "new_album_title", "new_album_description" ) ;

$post->require_true( strlen( $_POST['title']) < 76 , 'title', 'Title must be 75 characters or less.' ) ;
$post->require_true( strlen( $_POST['description']) < 256, 'description', 'Description must be 75 characters or less.' ) ;

$post->checkpoint() ;

$_POST['title'] = prevent_html( $_POST['title'] );
$_POST['description'] = prevent_html( $_POST['description'] );

$s = $db->build_set_string_from_post( 'title', 'description', 'privacy' ) ;


if( $apiCall == "uploadPhoto" ){
    
    $check = getimagesize( $_FILES["photo"]["tmp_name"] );
    
    $post->require_true( $check != false, 'image', 'This is not a valid image file.' ) ;
    
    $post->checkpoint() ;
        
    $imageFileType = pathinfo( $_FILES["photo"]["name"], PATHINFO_EXTENSION) ;
            
    if( ! in_array( $imageFileType, ['jpg','JPG','jpeg','JPEG','png','PNG' ] ) ) {
    
        $post->set_error("image", "Only .jpg, .jpeg, and .png files are allowed. ".$_FILES["photo"]["tmp_name"] ) ;
        unlink($_FILES["photo"]["tmp_name"] ) ;
    }

    $post->checkpoint();
    
    $filekey = store_uploaded_photo( $_FILES['photo']['tmp_name'] ) ;

    $u['file_key'] = $filekey ;
    $u['owner'] = $user->id ;
    
    $s .= ", ".$db->build_set_string_from_array($u).", `ip`='".get_client_ip()."', `timestamp`='".oe_time()."'" ;
        
    $_POST['photo_id'] = $db->insert( "INSERT INTO `".$oepc[$tier]['photo']['table']."` SET ".$s.", ".build_api_set_string() );
    
    verify_update( $oepc[$tier]['photo']['view'], $_POST['photo_id'] ) ;
    
} else {
    
    if( ! isset( $_POST['photo_id'] ) ){
        $post->json_reply("FAIL") ;
        die('invalid');
    }
    
    $db->update( "UPDATE `".$oepc[$tier]['photo']['table']."` SET ".$s."
                    WHERE ".build_api_where_string()."
                    AND `id`='".$_POST['photo_id']."'" ) ;
    
}
// avatar check



if( $oepc[0]['admin'] == true ){
    
    $oldAvatar = $db->get_assoc( "SELECT `avatar`, `file_key`
                                    FROM `".$oepc[$tier]['photo']['avatarView']."` , `".$oepc[$tier]['photo']['view']."`
                                    WHERE `".$oepc[$tier]['photo']['avatarView']."`.`".$oepc[$tier]['photo']['avatarID']."`='".$oepc[$tier]['id']."'
                                    AND `".$oepc[$tier]['photo']['avatarView']."`.`avatar` = `".$oepc[$tier]['photo']['view']."`.`id`" ) ;
    
    if( $_POST["parentavatar"] == "on"  ){
        
        if( $oldAvatar == false or $_POST['photo_id'] != $oldAvatar["avatar"] ){
            
            if( $apiCall == 'editPhoto'){
                // we need to look up the file key of THIS photo
                
                $filekey = $db->get_field("SELECT `file_key` FROM `".$oepc[$tier]['photo']['table']."`
                    WHERE ".build_api_where_string()."
                    AND `id`='".$_POST['photo_id']."'" ) ;
            }

            $filebase =  $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$filekey ;
            
            // create the smaller image for the profile page
            
            resize_png( $filebase.".png", $filebase.".profile.png", 
                             $oepc[$tier]['photo'][ 'profileImageWidth'], $oepc[$tier]['photo'][ 'profileImageHeight'] ) ;
            
            
            resize_png( $filebase.".png", $filebase.".profileThumb.png", 
                             $oepc[$tier]['photo'][ 'profileThumbWidth'], $oepc[$tier]['photo'][ 'profileThumbHeight'] ) ;
            
            
            
            // update the field
            
            $db->update( "UPDATE `".$oepc[$tier]['photo']['avatarTable']."` 
                    SET `avatar` = '".$_POST["photo_id"]."'
                    WHERE `".$oepc[$tier]['photo']['avatarID']."`='".$oepc[$tier]['id']."'" ); 
            
            // delete the old profile & profile thumb
    
            if( $oldAvatar != false ) {

                unlink( $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$oldAvatar['file_key'].".profile.png"  );
                unlink( $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$oldAvatar['file_key'].".profileThumb.png"  );
            }
            
        }
        // else nothing.  It's already the avatar.
        
    } elseif( $oldAvatar['avatar'] == $_POST['photo_id'] ) {
    
        // they've unchecked the avatar button
        
        // remove the avatar from the parent item 
    
        $db->update( "UPDATE `".$oepc[$tier]['photo']['avatarTable']."` 
                SET `avatar` = NULL
                WHERE `".$oepc[$tier]['photo']['avatarID']."`='".$oepc[$tier]['id']."'" );
        
        // delete the old avatar files
        
        unlink( $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$oldAvatar['file_key'].".profile.png"  );
        unlink( $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$oldAvatar['file_key'].".profileThumb.png"  );
            
        
    }

   
}
if( $oepc[$tier]['photo']['useAlbums'] ){
    
    // include( $oe_plugins['album']."/includes/albumprocessor.php" );
    
}

if( $apiCall == "uploadPhoto" ){
    verify_update( $oepc[$tier]['photo']['view'], $_POST['photo_id']) ;
    
    $return = str_replace( 'upload', $_POST["photo_id"], $_SERVER['HTTP_REFERER'] );
    $post->json_reply( 'SUCCESS', [ 'photo_id' => $_POST['photo_id'] ] )  ;
} else {
    
    $return = str_replace( '/edit', '', $_SERVER['HTTP_REFERER'] );
    $post->json_reply( 'SUCCESS' ) ;
}

header( 'Location: '.$return ) ;
die();

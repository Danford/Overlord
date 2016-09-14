<?php

/*
 *  since much of edit and upload are the same, they are handled here together.
 * 
 */


$post->checkbox('parentavatar') ;
$post->checkbox('setalbumavatar') ;

$post->hold( "private", "title", "description", "parentavatar", "setalbumvatar", "album", 
             "new_album_title", "new_album_description" ) ;

$post->require_true( strlen( $_POST['title']) < 76 , 'title', 'Title must be 75 characters or less.' ) ;
$post->require_true( strlen( $_POST['description']) < 256, 'description', 'Description must be 75 characters or less.' ) ;

$post->checkpoint() ;

$_POST['title'] = prevent_html( $_POST['title'] );
$_POST['description'] = prevent_html( $_POST['description'] );

$s = $db->build_set_string_from_post( 'title', 'description', 'privacy' ) ;

$o['file_key'] = $filekey ;
$o['owner'] = $user->id ;

$s .= ", ".$db->build_set_string_from_array( $o ) ;


if( $apiCall == "uploadPhoto " ){
    
    $check = getimagesize( $_FILES["photo"]["tmp_name"] );
    
    $post->require_true( $check != false, 'image', 'This is not a valid image file.' ) ;
    
    $post->checkpoint() ;
    
    $imageFileType = pathinfo( $oepc[$tier]['photo'].['path'].$_FILES["photo"]["name"], PATHINFO_EXTENSION) ;
        
    if( ! in_array( $imageFileType, ['jpg','JPG','jpeg','JPEG','png','PNG' ] ) ) {
    
        $post->set_error("image", "Only .jpg, .jpeg, and .png files are allowed. ".$_FILES["photo"]["name"] ) ;
        unlink($_FILES["photo"]["tmp_name"] ) ;
    }

    $post->checkpoint();
    
    include( $oe_plugins['photo']."lib/photo.lib.php" ) ;
    
    $filekey = store_uploaded_photo( $_FILES['photo']['name'] ) ;
    
    $s .= ", `ip`='".$_SERVER['REMOTE_ADDR']."', `timestamp`='".oe_time()."'" ;
    
    $_POST['photo_id'] = $db->insert( "INSERT INTO `".$oepc[$tier]['photo']['table']." SET ".$s );
    
    verify_update( $oepc[$tier]['photo']['view'], $_POST['photo_id'], 'id' ) ;
    
} elseif( ! isset( $_POST['photo_id'] ) ){
    
    $post->json_reply("FAIL") ;
    die();
}

// avatar check
    
if( $_POST["parentavatar"] == "on" and $oepc[0]['admin'] == true ){

    $oldAvatar = $db->get_assoc( "SELECT `avatar`, `filekey` 
                                    FROM `".$oepc[$tier]['photo']['avatarView']."` , `".$oepc[$tier]['photo']['view']."`
                                    WHERE `".$oepc[$tier]['photo']['avatarView']."`.`id`='".$oepc[$tier]['id']."'
                                    AND `".$oepc[$tier]['photo']['avatarView']."`.`id` = `".$oepc[$tier]['photo']['view']."`.`id`" ) ;

    if( $_POST['photo_id'] != $oldAvatar["avatar"] ){
        
        // create the new profile thumb
        
        $filebase =  $oepc[$tier]['path'].$oepc[$tier]['type'].".".$_POST['photo_id'].".".$filekey ;
        

        resize_png( $filebase.".png", $filebase.".profile.png", 
                         $oepc[$tier]['photo'][ 'profileImageSize'], $oepc[$tier]['photo'][ 'profileImageSize'] ) ;
        resize_png( $filebase.".png", $filebase.".profileThumb.png", $oepc[$tier]['photo'][ 'profileThumbSize'] ) ;
        
        
        
        // update the field
        
        $db->update( "UPDATE `".$oepc[$tier]['photo']['avatarTable']."` 
                SET `avatar` = '".$_POST["photo_id"]."'
                WHERE `id`='".$oepc[$tier]['id']."'" ); 
        
        // delete the old profile & profile thumb

        unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$oldAvatar['filekey'].".profile.png"  );
        unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$oldAvatar['filekey'].".profileThumb.png"  );
        
        
    }
    // else nothing.  It's already the avatar.
    
}


if( $oepc[$tier]['photo']['useAlbums'] ){
    
    // include( $oe_plugins['album']."/includes/albumprocessor.php" );
    
}

if( $apiCall == "uploadPhoto" ){

    $return = str_replace( $_POST["photo_id"], "upload", $_SERVER['HTTP_REFERER'] );
    $post->json_reply( "SUCCESS", [ 'photo_id' => $_POST['photo_id'] ] )  ;
} else {

    $return = str_replace( $_POST["photo_id"], "edit", $_SERVER['HTTP_REFERER'] );
    $post->json_reply( "SUCCESS" ) ;
}

header( 'Location: '.$return ) ;
die();

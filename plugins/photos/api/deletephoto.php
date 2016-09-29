<?php

    // is this picture an avatar?  
    
$itemAvatar = $db->get_assoc( "SELECT `owner`,`avatar`, `file_key`
                                FROM `".$oepc[$tier]['photo']['avatarView']."` , `".$oepc[$tier]['photo']['view']."`
                                WHERE `".$oepc[$tier]['photo']['avatarView']."`.`id`='".$oepc[$tier]['id']."'
                                AND `".$oepc[$tier]['photo']['avatarView']."`.`id` = ".$oepc[$tier]['photo']['view']."`.`id`" ) ;

if( $itemAvatar == false or ( ! $oepc[$tier]['admin'] and $itemAvatar['owner'] != $user->id()) ){
    $post->json_reply("FAIL") ;
    die();
}    
    
if( $_POST['photo_id'] == $itemAvatar["avatar"] ){
    
    $filekey = $itemAvatar['filekey'] ;
    
    $db->update( "UPDATE `".$oepc[$tier]['photo']['avatarView']."` SET `avatar`= NULL" );
    unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$filekey.".profile.png"  );
    unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$filekey.".profileThumb.png"  );
    
}


/* ---------------------------------------------
 * 
 * album avatar??
 * 
 * is it in an album?
 * 
 * ---------------------------------------------
 */



if( ! isset( $filekey ) ){
    
    $filekey = $db->get_field( "SELECT `file_key` FROM `".$oepc[$tier]['photo']['view']."` 
                                WHERE `id`='".$_POST['photo_id']."'" );
}




$db->update( "DELETE FROM `".$oepc[$tier]['photo']['table']."` WHERE `id`='".$_POST['photo_id']."'" );

unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$filekey.".png"  );
unlink( $oepc[$tier]['path'].$oepc[$tier]['type'].".".$oepc[$tier]['type'].".".$filekey.".thumb.png"  );

$post->reply( "SUCCESS" );

header( "Location:  ".preg_replace( $_POST['photo_id'].".png", "", $_SERVER['HTTP_REFERER'])) ;






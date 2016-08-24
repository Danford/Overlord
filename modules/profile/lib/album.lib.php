<?php

/**
 * Decrements album count and updates user_profile as needed
 * Where type and private refer to the item being added to the album
 *
 */
function decrement_album( $album_id , $type, $private ){
    
    global $db ;
    global $user ;
    $old = $db->get_assoc( "SELECT `public_photo` + `public_prose` + `public_video` as `public`, `private_photo` + `private_prose` + `private_video` as `private`
						FROM `profile_albums` WHERE `album_id`='".$album_id."' and `owner`='".$user->id."'" ) ;

    if( $old != false ){
    
        $q = "UPDATE `profile_albums` SET `private_".$type."` = `private_".$type."` - 1" ;
        
        if( ! $private ){
            $q .= ", `public_".$type."` = `public_".$type."` - 1" ;
        } 
            
        $q .= " WHERE `album_id` = '".$album_id."'" ;
        
        $db->update( $q );
        
        if( $private ){
            if( $old['private'] == 1 ){
                /* we've removed the last private item. Since public items are also private items,
                     and this was a private item, there were no public items.
                     Decrement private_albums in user_profile */
                $db->update( "UPDATE `user_profile` 
                    SET `total_private_albums` = `total_private_albums` - 1
                    WHERE `user_id`='".$user->id."'" ) ;
            } 
        } elseif( $old['private'] == 1 ){
            /* There are no private items remaining.
             * Decrement both private and public albums */
            $db->update( "UPDATE `user_profile`
            SET `total_private_albums` = `total_private_albums` - 1,
            SET `total_public_albums` = `total_public_albums` - 1
            WHERE `user_id`='".$user->id."'" ) ;
        } elseif( $old['public'] == 1 ) {
            /* Decrement just public albums */
            $db->update( "UPDATE `user_profile`
            SET `total_public_albums` = `total_public_albums` - 1
            WHERE `user_id`='".$user->id."'" ) ;
        }
    }
}

/**
 * Increments album count and updates user_profile albums count as needed
 * Where type and private refer to the item being added to the album
 * 
 */
function increment_album( $album_id , $type, $private ){
    
    global $db ;
    global $user ;
    
    $old = $db->get_assoc( "SELECT `public_photo` + `public_prose` + `public_video` as `public`, `private_photo` + `private_prose` + `private_video` as `private`
						FROM `profile_albums` WHERE `album_id`='".$album_id."' and `owner`='".$user->id."'" ) ;
   
    if( $old != false ){
        
        $q = "UPDATE `profile_albums` SET `private_".$type."` = `private_".$type."` + 1" ;
        
        if( ! $private ){
            $q .= ", `public_".$type."` = `public_".$type."` + 1" ;
        } 
            
        $q .= ", `last_updated` = '".oe_time()."' WHERE `album_id` = '".$album_id."'" ;
        
        $db->update( $q );
        
        if( $private ){
            if( $old['private'] == 0 ){
                /* This album was empty -- increment private count */
                $db->update( "UPDATE `user_profile` 
                    SET `total_private_albums` = `total_private_albums` + 1
                    WHERE `user_id`='".$user->id."'" ) ;
           
            } 
        } elseif( $old['private'] == 0 ){
            /* empty album -- incremement both */ 
                $db->update( "UPDATE `user_profile`
                SET `total_private_albums` = `total_private_albums` + 1,
                     `total_public_albums` = `total_public_albums` + 1
                WHERE `user_id`='".$user->id."'" ) ;
       } elseif( $old['public'] == 0 ) {
            /* album had private stuff already, just increment public */
                $db->update( "UPDATE `user_profile`
                SET `total_public_albums` = `total_public_albums` + 1
                WHERE `user_id`='".$user->id."'" ) ;
        }
    }
}

/**
 * 
 * Creates an album and increments user_profile album count(s)
 * 
 * Private and type refer to the item being added to the new album.
 * 
 */
function create_album( $title, $description, $type, $private ){

    global $db ;
    global $user ;
    
    $a['owner'] = $user->id ;
    $a['title'] = $title ;
    $a['description'] = $description ;
    
    $q = "UPDATE `user_profile` SET `total_private_albums` = `total_private_albums` + 1" ;
    
    if( $_POST['private'] == 0 ) {
        $a['public_'.$type ] = 1 ;
        $q .= ", `total_public_albums` = `total_public_albums` + 1" ;
    }
    
    $a['private_photo'] = 1 ;
    
    $albumid = $db->insert( "INSERT INTO `profile_albums` SET ".$db->build_set_string_from_array($a) ) ;
    
    $db->update( $q." WHERE `user_id`='".$user->id."'" ) ;
    
    return $albumid ;
}

function increment_profile_item_count( $type, $private ){
    
    global $db ;
    global $user ;
    
    $q = "UPDATE `user_profile` SET `total_private_".$type."` = `total_private_".$type."` + 1" ;
    
    if( ! $private ) {
        $q .= ", `total_public_".$type."` = `total_public_".$type."` + 1" ;
    }
    
    $q .= " WHERE `user_id`='".$user->id."'" ;
    
    $db->update( $q ) ;
}

function decrement_profile_item_count( $type, $private ){
    
    global $db ;
    global $user ;
    
    $q = "UPDATE `user_profile` SET `total_private_".$type."` = `total_private_".$type."` - 1" ;
    
    if( ! $private ) {
        $q .= ", `total_public_".$type."` = `total_public_".$type."` - 1" ;
    }
    
    $q .= " WHERE `user_id`='".$user->id."'" ;
    
    $db->update( $q ) ;
}

























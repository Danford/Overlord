<?php

// this script is for accessing protected images.  If they ain't logged in, they ain't gettin' nothin'.
// similarly, just accessing the script with no parameters, nothin'.


if( $user->id == 0  or ! isset( $uri[$pos] ) or $uri[$pos] == '' ) { die() ; }

// the parameter passed isn't a filename, but rather a session key.  
// if that isn't set.
//

$type = $uri[$pos] ;

$pos++ ;

$id = substr( $uri[$pos], 0, strpos($uri[$pos], ".") -1 ) ;


switch ( $type ){ 

    case 'avatar': // picture as on profile page
    
        $x = $db->get_assoc( "SELECT `owner`, `private`, `file_key` FROM `profile_photo` WHERE `photo_id`='".$id."'" ) ;
        
        if( $x == false ){ die() ; }
        
        if( $x['private'] == 0 or $user->id == $x['owner'] or $user->is_friend( $x['owner'] ) ) {
            $image = 'user.'.$x['owner'].'.'.$x['file_key'].'.profile.png' ;
        }
    
    
    break ;
    
    case 'userimage':  // picture user has uploaded
    
        $x = $db->get_assoc( "SELECT `owner`, `private`, `file_key` FROM `profile_photo` WHERE `photo_id`='".$id."'" ) ;
        
        if( $x == false ){ die() ; }
        
        if( $x['private'] == 0 or $user->id == $x['owner'] or $user->is_friend( $x['owner'] )  ) {
            $image = 'user.'.$x['owner'].'.'.$x['file_key'].'.png' ;
        }
    
    break ;
    
    case 'userthumb': //thumbnail picture of userimage
    
        $x = $db->get_assoc( "SELECT `owner`, `private`, `file_key` FROM `profile_photo` WHERE `photo_id`='".$id."'" ) ;
        
        if( $x == false ){ die() ; }
        
        if( $x['private'] == 0 or $user->is_friend( $x['owner'] ) or $user->id == $x['owner'] ) {
    
            $image = 'user.'.$x['owner'].'.'.$x['file_key'].'.thumb.png' ;
        }
    
    break ;
    
    case 'profilethumb': //thumbnail picture of avatar
    
        $x = $db->get_assoc( "SELECT `owner`, `private`, `file_key` FROM `profile_photo` WHERE `photo_id`='".$id."'" ) ;
    
        if( $x['private'] == 0 or $user->is_friend( $x['owner'] or $user->id == $x['owner'] ) ) {
    
            $image = 'user.'.$x['owner'].'.'.$x['file_key'].'.profilethumb.png' ;
        }    
    
    break ;

    
}

if( isset( $image ) and file_exists( ul_img_dir.$image ) ) {

    header("Content-Type: image/png");
    header("Content-Length: " . filesize(ul_img_dir.$image ));
    $file = @fopen( ul_img_dir.$image , "rb" ) ;

    fpassthru( $file );
    fclose($file) ;
}

die() ;
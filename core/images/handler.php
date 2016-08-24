<?php

// this script is for accessing protected images.  If they ain't logged in, they ain't gettin' nothin'.
// similarly, just accessing the script with no parameters, nothin'.


if( $user->id == 0  or ! isset( $uri[$pos] ) or $uri[$pos] == '' ) { die() ; }

// the parameter passed isn't a filename, but rather a session key.  
// if that isn't set.
//

if( ! isset( $_SESSION["imagekey"][$uri[$pos]] ) ){ die('there'); }

if( file_exists( ul_img_dir.$_SESSION["imagekey"][$uri[$pos]] ) ) {
    
    header("Content-Type: image/png");
    header("Content-Length: " . filesize(ul_img_dir.$_SESSION["imagekey"][$uri[$pos]]));
    $file = @fopen( ul_img_dir.$_SESSION["imagekey"][$uri[$pos]], "rb" ) ;
    
    fpassthru( $file );
    
    fclose($file) ;
    
    unset( $_SESSION["imagekey"][$uri[$pos]] ) ;

}


<?php

    /*
     *  This defines the default configurations for THIS plugin.
     *  
     *  They can be overridden by the component that it is attached to via
     *  that component's plugin.conf.php.
     *   
     *   
     *   
     */


$default['table'] = 'photo' ; 

    // the database table used for inserts & updates
    
$default['view'] = 'photo' ;

    // the database table or view used for queries

$default['useAlbums'] = false ;

    // exactly what it says on the tin.

$default['path'] = oe_root.'oe_images/' ;

    // This dictates where on the server the photo is uploaded.

$default['avatarTable'] = $oepc[$tier]['type'] ; 

    // the table to change the existing avatar

$default['avatarView'] = $oepc[$tier]['type'] ;

    // the view to check the existing avatar

$default['avatarID'] = 'id' ;

    // the primary key of the table or view above

// maximum dimensions of uploaded images in pixels

$default[ 'maxImageWidth'] = 1000 ;
$default[ 'maxImageHeight'] = 800 ;
$default[ 'thumbnailSize'] = 125 ;
$default[ 'profileImageSize'] = 300 ;
$default[ 'profileThumbSize'] = 75 ;

// insert into settings if not already defined.

foreach( $default as $setting => $value ){
    if( ! isset( $oepc[$tier]['photo'][$setting] ) ){
        $oepc[$tier]['photo'][$setting] = $value ;
    }
}
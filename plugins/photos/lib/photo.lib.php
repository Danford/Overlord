<?php


function convert_and_size_jpg( $original, $destination, $max_width = 0, $max_height = 0 ) {

    $source = imagecreatefromjpeg($original);
    list( $width, $height ) = getimagesize($original);
    $exif = exif_read_data($original);

    // Calculate final size

    if ($width > $max_width or $height > $max_height){

        $ratio = $width / $height;

        if ($ratio < 1) {

            $new_height = $max_height;
            $new_width = $new_height * $ratio;

        } else {

            $new_width = $max_width;
            $new_height = $new_width / $ratio;

        }

    } else {

        $new_width = $width ;
        $new_height = $height ;
    }

    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Resize
    imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);


    if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
            case 8:
                $new_image = imagerotate($new_image,90,0);
                break;
            case 3:
                $new_image = imagerotate($new_image,180,0);
                break;
            case 6:
                $new_image = imagerotate($new_image,-90,0);
                break;
        }
    }

    // Output
    imagepng( $new_image, $destination );
}

function resize_png( $original, $destination, $max_width = 0, $max_height = 0 ) {

    $source = imagecreatefrompng($original);
    list( $width, $height ) = getimagesize($original);

    if ($width > $max_width or $height > $max_height){

        $ratio = $width / $height;

        if ($ratio < 1) {

            $new_height = $max_height;
            $new_width = $new_height * $ratio;

        } else {

            $new_width = $max_width;
            $new_height = $new_width / $ratio;

        }

    } else {

        $new_width = $width ;
        $new_height = $height ;
    }


    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Resize
    imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Output
    imagepng( $new_image, $destination );

}
function create_square_thumb($original, $destination, $dimension ){

    list( $original_width, $original_height ) = getimagesize($original);

    if( $original_width > $original_height ){

        $new_height = $dimension;
        $new_width = round( $new_height * ( $original_width / $original_height ) );

    } elseif( $original_height > $original_width ){

        $new_width = $dimension;
        $new_height = round( $new_width * ( $original_height / $original_width ) );

    } else {

        $new_width = $dimension;
        $new_height = $dimension;
    }
    
    $source = imagecreatefrompng( $original );
    $scaled = imagecreatetruecolor( $new_width, $new_height );

    imagecopyresized( $scaled, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

    $thumbnail = imagecreatetruecolor( $dimension, $dimension );

    if( $new_width > $new_height ){

        $difference = $new_width - $new_height;
        $half_difference =   round( $difference / 2 );
        imagecopyresized($thumbnail, $scaled, 1 - $half_difference, 0, 0, 0, $dimension + $difference, $dimension, $new_width, $new_height );

    } elseif( $new_height > $new_width ){

        $difference = $new_height - $new_width;
        $half_difference =  round( $difference / 2 );
        imagecopyresized( $thumbnail, $scaled, 0, 1 - $half_difference, 0, 0, $dimension, $dimension + $difference, $new_width, $new_height );

    } else {

        imagecopyresized($thumbnail, $scaled, 0, 0, 0, 0, $dimension, $dimension, $new_width, $new_height);

    }

    imagepng( $thumbnail,$destination );

    imagedestroy( $source );
    imagedestroy( $scaled );
    imagedestroy( $thumbnail );

}


function store_uploaded_photo( $filename ){
    
    global $tier ;
    global $oepc ;
    
    $imageFileType = pathinfo( $filename, PATHINFO_EXTENSION) ;

    $filekey = createGUID() ;
    
    $newFilename = $oepc[0]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$filekey  ;
    
    if( $imageFileType == "png" or $imageFileType == "PNG" ) {
    
        resize_png( $filename, $newFilename.".png" ) ;
    
    } else {
    
        convert_and_size_jpg( $filename, $newFilename.".png" , $oepc[$tier]['photo']['maxImageWidth'], $oepc[$tier]['photo']['maxImageHeight'] ) ;
    
    }
    
    // create thumbnail
    
    create_square_thumb( $newFilename.".png" , $newFilename.".thumb.png", $oepc[$tier]['photo']['profileThumbSize'] );
    
    // delete the original
    
    unlink( $filename ) ;
    
    return $filekey ;
    
    
}

function get_photos( $start = 0, $end = 9999, $album = null ) {
    
    // album null -- all photos, regardless of album
    // album 0 - only photos not in albums
    
    global $db ;
    global $tier ;
    global $oepc ;
    global $accesslevel ;
    global $user ;
    global $post ;
    
    if( ! verify_number( $start ) or ! verify_number( $end ) ){
        
        if( isset( $post->is_a_json_request ) ){        
            $post->json_reply("FAIL") ;
        }
        die() ;
    }
    
    if( $album != null and ! verify_number( $album ) ){
        
        if( isset( $post->is_a_json_request ) ){        
            $post->json_reply("FAIL") ;
        }
        die() ;
    }
    
    
    $q = "SELECT `id`,`owner`, `album`, `privacy`, `title`, `description`, `timestamp`
                    FROM ".$oepc[$tier]['photo']['view']."
                    WHERE ".build_api_where_string() ;
    
    if( $album == 0 ){
        $q .= " AND `album`= NULL" ;
    } elseif( $album != null ) {
        $q .= " AND `album`='".$album."'" ;
    }
    
    $response = array() ;
    
    $db->query( $q." ORDER BY `timestamp` DESC LIMIT ".$start.", ".$end ) ;
    
    while( ( $photo = $db->assoc() ) != false ){
        
        if( ! $photo['privacy'] > $accesslevel and ! $user->is_blocked( $photo['owner'] ) )
        {
            $photo["owner"] = new profile_minion($photo["owner"], true );
            $response[] = $photo ;
        }
    }
    
    return $response ;
    
}


function get_photo_info( $photo_id ) {

    global $db ;
    global $tier ;
    global $oepc ;
    global $accesslevel ;
    global $user ;

    if( ! verify_number( $photo_id ) ){

        return false ;
    } else {

        
        $q = "SELECT `id`,`owner`, `privacy`, `album`, `title`, `description`, `timestamp`
                        FROM ".$oepc[$tier]['photo']['view']."
                        WHERE `module`='".$oepc[0]['type']."'
                          AND `module_id`='".$oepc[0]['id']."'" ;
        
        if( $tier > 0 ){
        
            $q .= "AND `plug`='".$oepc[$tier]['type']."'
                   AND `plug_id`='".$oepc[$tier]['id']."'" ;
        }
        
        $photo = $db->get_assoc( $q." AND `id`='".$photo_id."'" ) ;
        
        if( $photo != false and ! $photo['privacy'] > $accesslevel and ! $user->is_blocked( $photo['owner'] )){
        
            $photo["owner"] = new profile_minion($photo["owner"], true );
            return $photo ;    
        } else {
            return false ;
        }
    }
}
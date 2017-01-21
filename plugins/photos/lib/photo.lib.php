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

    $new_width = round($new_width, 0);
    $new_height = round($new_height, 0);
    
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Resize
    imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Delete existing file
	if (file_exists($destination)) {
    	unlink($destination);
    }
    
    // Output
    imagepng( $new_image, $destination, 9 );
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
    
    $newFilename = $oepc[$tier]['photo']['path'].$oepc[$tier]['type'].".".$oepc[$tier]['id'].".".$filekey  ;
    
    if( strcasecmp($imageFileType, "png") == 0) {
    
        resize_png( $filename, $newFilename.".png" ) ;
    
    } else {
    
        convert_and_size_jpg( $filename, $newFilename.".png" , $oepc[$tier]['photo']['maxImageWidth'], $oepc[$tier]['photo']['maxImageHeight'] ) ;
    
    }
    
    // create thumbnail
    
    resize_png( $newFilename.".png" , $newFilename.".thumb.png", $oepc[$tier]['photo']['thumbnailWidth'], $oepc[$tier]['photo']['thumbnailHeight'] );
    
    // delete the original
    
    unlink( $filename ) ;
    
    return $filekey ;
    
    
}

function get_photos( $start = 0, $end = 9999, $album = 'all' ) {
    
    // album 'all' -- all photos, regardless of album or lack thereof
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
        die( 'invalid start or end') ;
    }
    
    if( $album != 'all' and ! verify_number( $album ) ){
        
        if( isset( $post->is_a_json_request ) ){        
            $post->json_reply("FAIL") ;
        }
        die( 'invalid album' ) ;
    }
    
    
    $q = "SELECT `id`,`owner`, `album`, `privacy`, `title`, `description`, `timestamp`
                    FROM ".$oepc[$tier]['photo']['view']."
                    WHERE ".build_api_where_string() ;
    
    if( $album == 0 ){
        $q .= " AND `album` IS NULL" ;
    } elseif( $album != 'all' ) {
        $q .= " AND `album`='".$album."'" ;
    }
    
    
    $response = array() ;
    
    $db->query( $q." ORDER BY `timestamp` DESC LIMIT ".$start.", ".$end ) ;
    
    while( ( $photo = $db->assoc() ) != false ){

    	// bad hackky I'll revisit this with Cat.
    	$hasPerms = false;
    	if ($oepc[0]['type'] == 'group')
    	{
    		global $group;
    		if ($photo['privacy'] <= $user->groupPermmisions[$group->id]) {
    			$hasPerms = true;
    		}
    	}
    	else if ($oepc[0]['type'] == 'profile')
    	{
    		if( $photo['privacy'] <= $accesslevel and ! $user->is_blocked( $photo['owner'] ) ) {
    			$hasPerms = true;
    		}
    	}
    	
    	if ($hasPerms) {
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

function regeneratePhotos() {

	global $db ;
	global $tier ;
	global $oepc ;
	global $accesslevel ;
	global $user ;

	$q = "SELECT `id`, `module`, `owner`, `privacy`, `album`, `title`, `description`, `file_key`, `timestamp` FROM `photo` WHERE TRUE";
	
	$db->query($q);
	
	while (($photo = $db->assoc()) != false) {
		$imgPath = oe_images . $photo['module'] .".". $photo['owner'] .".". $photo['file_key'];
		
		if (!file_exists($imgPath .".png")) {
			echo "Error: ". $imgPath ." didn't exist.<br>\r\n";
			continue;
		}
		
		unlink($imgPath .".thumb.png");
		resize_png($imgPath .".png", $imgPath .".thumb.png", $oepc[$tier]['photo']['thumbnailWidth'], $oepc[$tier]['photo']['thumbnailHeight']);
		echo "Resized thumb ". $oepc[$tier]['photo']['thumbnailWidth'] ."<br>\r\n";
		
		if (file_exists($imgPath .".profile.png")) {
			unlink($imgPath .".profile.png");
			resize_png($imgPath .".png", $imgPath .".profile.png", $oepc[$tier]['photo']['profileImageWidth'], $oepc[$tier]['photo']['profileImageHeight']);
			echo "Resized ". $imgPath ." profile<br>\r\n";
		}
		
		if (file_exists($imgPath .".profileThumb.png")) {
			unlink($imgPath .".profileThumb.png");
			resize_png($imgPath .".png", $imgPath .".profileThumb.png", $oepc[$tier]['photo']['profileThumbWidth'], $oepc[$tier]['photo']['profileThumbHeight']);
			echo "Resized ". $imgPath ." profileThumb<br>\r\n";
		}
	}
	
}
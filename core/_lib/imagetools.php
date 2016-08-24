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


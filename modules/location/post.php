<?php

    include( $oe_modules['location']."lib/location.lib.php" ) ;
    
    switch( $_POST["oe_formid"] ) {
        
        case "getCityFromZip":
            
            $result = getCityFromZip( $_POST['zip'] ) ;
            
            if( $result == false ){
                $post->json_reply( "ERROR" ) ;
            }
            
            $post->json_reply( "SUCCESS", $result ) ;
            
        case "getCityFromName":

            $result = getCityFromName( $_POST['city'] ) ;
            
            if( $result == false ){
                $post->json_reply( "ERROR" ) ;
            }
            
            $post->json_reply( "SUCCESS", [ 'matches' => $result ] ) ;
            
        case "getZipsInBounds":

            $result = getZipsInBounds( $_POST['lat1'], $_POST['lon1'], $_POST['lat2'], $_POST['lon2'] ) ;
            
            if( $result == false ){
                $post->json_reply( "ERROR" ) ;
            }
            
            $post->json_reply( "SUCCESS", [ 'matches' => $result ] ) ;
            
    }
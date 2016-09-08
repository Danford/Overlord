<?php

function getCityFromZip( $zip ) {
    
    global $db ;
    
    $zip = $db->sanitize( $zip ) ;
    
    $db->query( "SELECT `city_id`, `city`, `state` FROM `location_zip`, `location_city`
                    WHERE `location_zip`.`zip` = '".$zip."'
                      AND `location_zip`.`city_id` = `location_city`.`id`
                    LIMIT 1" ) ;
    
    if( $db->count() == 0 ){
        return false ;
    } else {
        return( $db->assoc() ) ;
    }
    
}

function getCityByName( $city, $limit = 5 ){
    
    global $db ;
    
    $city = $db->sanitize( $city ) ;
    
    $city = str_replace( [ ", " , " ," ], "," , $city ) ;
    
    $city = explode( ",", $city ) ;
    
    if( count( $city ) == 1 ){
        
        $db->query( "SELECT `city_id`, `city`, `state` FROM `location_city` WHERE `city` LIKE '%'.$city[0].'%' LIMIT ".$limit ) ;
        
    } else {
        
        $db->query( "SELECT `city_id`, `city`, `state` FROM `location_city` WHERE `city` LIKE '%'.$city[0].'%' AND `state` LIKE '".$city[1]."%' LIMIT ".$limit ) ;
        
    }

    if( $db->count() == 0 ){
        $result = $false ;
    } else {
        
        $result = [] ;
        
        while( ( $r = $db->assoc() ) != false ){
            $result[] = $r ;
        }
        
    }
    
    return $result ;
}

function getZipsInBounds( $lat1, $lon1, $lat2, $lon2 ){
    
    global $db ;
    
    $db->query( "SELECT `zip`, `city_id`, `city`, `state`, `lat`, `lon`, `timezone`, `dst` 
                    FROM `location_zip`, `location_city`
                    WHERE `location_zip`.`city_id` = `location_city`.`city_id`
                      AND `lat` >= '".$db->sanitize( $lat1 )."' AND `lat` <= '".$db->sanitize( $lat2 )."' 
                      AND `lon` >= '".$db->sanitize( $lon1 )."' AND `lon` <= '".$db->sanitize( $lon2 )."'"  );
    
    if( $db->count() == 0 ){
        $result = $false ;
    } else {
        
        $result = [] ;
        
        while( ( $r = $db->assoc() ) != false ){
            $result[] = $r ;
        }
        
    }
    
    return $result ;
}
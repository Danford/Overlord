<?php
function get_writings( $start = 0, $limit = 999999, $album = null ){
    
    global $db ;
    global $oepc ;
    global $tier ;
    global $accesslevel ;
    
    if( verify_number( $start ) and verify_number( $limit ) and ( $album == null or verify_number( $album ) ) ){
        
        $q = "SELECT `id`,`title`,`subtitle`,`privacy`, `timestamp`,`last_updated` FROM `".$oepc[$tier]['writing']['view']."` WHERE " ;
        $q .= build_api_where_string() ;
        
        $db->query( $q ) ;

        $response = [] ;
        
        while( ( $writing = $db->assoc() ) != false ){
            if( ! $accesslevel < $writing['privacy'] ){
                $response[] = $writing ;
            }
        }
       
        return $response ;
        
    } else {
        return false ;
    }
    
}
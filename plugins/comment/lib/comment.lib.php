<?php


function get_comments( $start = 0, $stop = 9999999 ){
 
    global $oepc ;
    global $tier ;
    global $db ;
    
    if( verify_number( $start ) and verify_number( $stop ) ){
    
        $q = "SELECT `id`, `comment`, `owner` FROM `comment`
                        WHERE `module`='".$oepc[0]['type']."'
                          AND `module_item_id`='".$oepc[0]['id']."'" ;
        
        if( $tier > 0 ){
            
            $q .= "AND `plug`='".$oepc[$tier]['type']."'
                   AND `plug_item_id`='".$oepc[$tier]['id']."'" ;
        }
        
        $q .= " LIMIT ".$start.", ".$stop;
        
        $db->query($q);
        
        while(($comment = $db->assoc()) != false){
            $comment["owner"] = new profile_minion($comment["owner"], true );
            $response[] = $comment ;
        }
        
        if( isset( $response )){
           return $response ;
        } 
    } else {
        return false ;
    }
       
}
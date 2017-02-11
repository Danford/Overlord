<?php


function get_comments( $start = 0, $stop = 9999999, $module = NULL, $module_id = NULL, $plug = NULL, $plug_id = NULL){
 
    global $oepc ;
    global $tier ;
    global $db ;
    
    if (!isset($module)) {
    	$module = $oepc[0]['type'];
    }
    
    if (!isset($module_id)) {
    	$module_id = $oepc[0]['id'];
    }
    
    if (!isset($plug)) {
    	$plug = $oepc[$tier]['type'];
    }
    
    if (!isset($plug_id)) {
    	$plug_id = $oepc[$tier]['id'];
    }
    
    
    if( verify_number( $start ) and verify_number( $stop ) ){
    	
        $q = "SELECT `id`, `comment`, `owner` FROM `comment`
                        WHERE `module`='".$module."'
                          AND `module_item_id`='".$module_id."'" ;

        if( $tier > 0 || isset($plug) && isset($plug_id)){
            
            $q .= "AND `plug`='".$plug."'
                   AND `plug_item_id`='".$plug_id."'" ;
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
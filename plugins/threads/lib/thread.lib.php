<?php

function get_threads( $start = 0, $limit = 9999999 ){
    
    global $oepc ;
    global $tier ;
    global $db ;
    global $user ;
    
    $response = array();
    
    $db->query( "SELECT `id`,`title`,`detail`,`sticky`,`locked`, `owner`, `edited`,
                        ( SELECT COUNT(*) FROM `comment` 
                            WHERE `module`='".$oepc[$tier]['type']."' 
                              AND `module_item_id`='".$oepc[$tier]['id']."'
                              AND `plug`='thread'
                              AND `plug_item_id`= `thread`.`id` ) as `msgcount`,
                        ( SELECT MAX(`timestamp`) FROM `comment` 
                            WHERE `module`='".$oepc[$tier]['type']."' 
                              AND `module_item_id`='".$oepc[$tier]['id']."'
                              AND `plug`='thread'
                              AND `plug_item_id`= `thread`.`id` ) as `last_updated`
                    FROM `thread`
                    WHERE ".build_api_where_string()."
                    ORDER BY `sticky` DESC, `updated` DESC
                    LIMIT ".$start.", ".$limit ) ;
    
    while( ( $thread = $db->assoc() ) != false ){
        
        if( ! $user->is_blocked( $thread['owner'] ) ){
            
            $thread['owner'] = new profile_minion( $thread['owner'], true ) ;
            $response[] = $thread ;
            
        }
    }
    
    return $response ;    
    
}
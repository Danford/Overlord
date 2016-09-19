<?php

if( $apiCall != 'like' ){
    
    $where = "`module`='".$basemodule."' AND `module_id`='".$basemoduleID."'" ;
        
    if( $tier > 0 ){
        $where .= "AND `plug` ='".$lastplug."'
        AND `plug_id`='".$lastplugID."'" ;
    }
}

switch( $apiCall ){
    
    case "like":
        
        if( $oepc[0]['contributor'] != true ){
        
            $post->json_reply("FAIL") ;
            die() ;
        
        }
        
        $o['module'] = $basemodule ;
        $o['module_id'] = $basemoduleID ;
        
        if( $tier > 0 ){
            $o['plug'] = $lastplug ;
            $o['plug_id'] = $lastplugID ;
        }
        
        $o['owner'] = $user->id ;
        $o['ip'] = get_client_ip() ;
        $o['timestamp'] = oe_time() ;
        
        $db->insert( "INSERT INTO `".$opec[$tier]['like']['table']."` 
                        SET ".$db->build_set_string_from_array($o)) ;

        $post->json_reply("SUCCESS") ;
        $post->return_to_form() ;
        
    case "unlike":
        
        $x = $db->get_field( "SELECT `owner` FROM `".$opec[$tier]['like']['view']."` WHERE ".$where ) ;
        
        if( $x == false or ( $user->id != x and $oepc[0]['admin'] != true )){
            $post->json_reply("FAIL") ;
            die() ;
        }
        
        $db->update( "DELETE ` FROM `".$opec[$tier]['like']['table']."` WHERE ".$where ) ;

        $post->json_reply("SUCCESS") ;
        $post->return_to_form() ;
        
        
    case "countLikes":

        $x = $db->get_field( "SELECT COUNT(*) FROM `".$opec[$tier]['like']['view']."` WHERE ".$where ) ;

        $post->json_reply("SUCCESS", [ 'count' => $x ] );
        die();
    
    case "getLikes":
        
        $db->query( "SELECT `owner` as `id`, `screen_name` 
                              FROM `".$opec[$tier]['like']['view']."` WHERE ".$where ) ;
        
        $x = array() ;
        
        while( ($l = $db->assoc() ) != false ){
            $x[] = $l ;
        }
        
        $post->json_reply("SUCCESS", $x );
        die();
        
    case 'doIlike':
        

        $x = $db->get_field( "SELECT COUNT(*) FROM `".$opec[$tier]['like']['view']."` WHERE ".$where." AND `owner`='".$user->id."'" ) ;
        
        if( $x == false ){
            $post->json_reply("FAIL") ;
        } elseif( $x == 0 ) {
            $post->json_reply("SUCCESS", [ 'iLike' => false]) ;            
        } else {
            $post->json_reply("SUCCESS", [ 'iLike' => true]) ;            
        }
    
        die() ;
    
    
    
    
}
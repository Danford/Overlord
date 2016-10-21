<?php

switch( $apiCall ){

    case 'addComment':
        
        if( $oepc[0]['contributor'] ){
            
            $s = $db->build_set_string_from_post( 'title', 'description', 'privacy' ) ;
            
            $o['owner'] = $user->id ;
            $o['module'] = $basemodule ;
            $o['module_id'] = $basemoduleID ;
            $o['comment'] = process_user_supplied_html( $_POST['comment'] ) ;
            $o['ip'] = get_client_ip() ;
            $o['timestamp'] = oe_time() ;
            
            if( $tier > 0 ){
                $o['plug'] = $lastplug ;
                $o['plug_id'] = $lastplugID ;
            }
            
            $cid = $db->insert( "INSERT INTO `comment` SET ".$db->build_set_string_from_array($o) ) ;
            
            verify_update($oepc[$tier]['comment']['view'], $cid ) ;
            
            $post->json_reply( "SUCCESS", [ 'id' => $cid ] ) ;
            
            if( $oepc[$tier]['comment']['page'] == false ) {
                $post->return_to_form() ; 
            } else {
                
                // pagination
                
                $q = "SELECT COUNT(*) FROM `comment`
                           WHERE `module`='".$basemodule."' 
                             AND `module_item_id`='".$basemoduleID."'" ;
                
                if( $$tier > 0 ){
                    $q .= " AND `plug`='".$lastplug."'
                            AND `plug_id`='".$lastplugID."'" ;
                }
                
                $c = $db->get_field( $q ) ;

                $page = ( ($c - ( $c % $oepc[$tier]['comment']['page'] )) / $oepc[$tier]['comment']['page'] ) + 1 ;
                
                if( $page == 1 ){ $post->return_to_form() ; }
                
                if( preg_match( '/page\/[0-9]*\/{0,1}$/', $_SERVER['HTTP_REFERER']) == 0 ){
                    
                    if( substr( $_SERVER['HTTP_REFERER'], -1 ) == "/" ){
                        header( "Location: ".$_SERVER['HTTP_REFERER']."page/". $page ) ;
                    } else {
                        header( "Location: ".$_SERVER['HTTP_REFERER']."/page/". $page ) ;
                    }
                    
                } else {
                    header( "Location: ".preg_replace('/[0-9]*\/{0,1}%/', $page, $_SERVER['HTTP_REFERER'] ) ) ;
                }
                
                die() ;
            }
            
        } else {
            $post->json_reply("FAIL") ;
            die() ;
        }
        
    case 'deleteComment':

        if( verify_number( $_POST['comment_id'] ) ){
            $post->json_reply("FAIL") ;
            die() ;
        }
        
        
        $q = "SELECT `owner` FROM `comment` 
                WHERE `id`='".$_POST['comment_id']."'
                AND `module` = '".$basemodule."' AND `module_id`='".$basemoduleID."'" ;
        
        if( $tier > 0 ){
            $q .= "AND `plug`='".$lastplug."' and `lastplug`='".$lastplugID."'" ;
        }
    
        $owner = $db->get_assoc( $q ) ;

        if( $owner != false and ( $oepc[0]['admin'] or $owner == $user->id )){
            $db->update( "DELETE FROM `comment` WHERE `id`='".$_POST['comment_id']."'" ) ;
        
            $post->json_reply( "SUCCESS" ) ;
            $post->return_to_form() ;
        }
        
        $post->json_reply("FAIL") ;
        die() ;
        
    case 'getComments':
        
        // this is a json-only call
        
        if( verify_number( $_POST['start'] ) and verify_number( $_POST['limit' ]) ){
            
            include_once $oe_plugins['comment']."lib/comment.lib.php";
            
            $post->reply( "SUCCESS", get_comments( $_POST['start'], $_POST['limit'] ) );
            
        } else {
            $post->json_reply("FAIL") ;
            die() ;
        }
}

